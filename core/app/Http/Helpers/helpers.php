<?php

use App\Models\User;

use App\Models\Payment;
use App\Models\Ranking;
use App\Models\Refferal;
use App\Models\Advertise;
use App\Models\SectionData;
use App\Models\EmailTemplate;
use App\Models\GeneralSetting;
use App\Models\UserDesignation;
use App\Models\RefferedCommission;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Log;

function makeDirectory($path)
{
    if (file_exists($path)) return true;
    return mkdir($path, 0755, true);
}



function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}

function fileUpload($file, $location)
{
    $path = makeDirectory($location);

    if (!$path) throw new Exception('File could not been created.');

    if (!empty($old)) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }

    $filename = $file->getClientOriginalName();


        $file->move($location, $filename);
        $location = $location . '/' . $filename;


    return $location;
}

function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
{

    $path = makeDirectory($location);

    if (!$path) throw new Exception('File could not been created.');

    if (!empty($old)) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();


    if ($file->getClientOriginalExtension() == 'gif') {
        copy($file->getRealPath(), $location . '/' . $filename);
    } else {

        $image = Image::make($file);

        if (!empty($size)) {
            $size   = explode('x', strtolower($size));

            $canvas = Image::canvas(400, 400);

            $image = $image->resize(400, 400, function ($constraint) {
                $constraint->aspectRatio();
            });

            $canvas->insert($image, 'center');
            $canvas->save($location . '/' . $filename);
        } else {
            $image->save($location . '/' . $filename);
        }

        if (!empty($thumb)) {
            $thumb = explode('x', $thumb);
            Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
        }
    }

    return $filename;
}





function menuActive($routeName)
{

    $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = 0;
    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }
    return random_int($min, $max);
}

function gatewayImagePath()
{

    $general = GeneralSetting::first();

    return "asset/theme{$general->theme}/images/gateways";
}

function filePath($folder_name)
{
    $general = GeneralSetting::first();

    return "asset/theme{$general->theme}/images/" . $folder_name;
}


function frontendFormatter($key)
{
    return ucwords(str_replace('_', ' ', $key));
}


function getFile($folder_name, $filename)
{
    $general = GeneralSetting::first();

    if (file_exists(filePath($folder_name) . '/' . $filename) && $filename != null) {

        return asset("asset/theme{$general->theme}/images/" . $folder_name . '/' . $filename);
    }

    return asset("asset/theme{$general->theme}/images/placeholder.png");
}

function variableReplacer($code, $value, $template)
{
    return str_replace($code, $value, $template);
}

function sendGeneralMail($data)
{
    $general = GeneralSetting::first();


    if ($general->email_method == 'php') {
        $headers = "From: $general->sitename <$general->site_email> \r\n";
        $headers .= "Reply-To: $general->sitename <$general->site_email> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        @mail($data['email'], $data['subject'], $data['message'], $headers);
    } else {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $general->email_config->smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $general->email_config->smtp_username;
            $mail->Password   = $general->email_config->smtp_password;
            if ($general->email_config->smtp_encryption == 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port       = $general->email_config->smtp_port;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($general->site_email, $general->sitename);
            $mail->addAddress($data['email'], $data['name']);
            $mail->addReplyTo($general->site_email, $general->sitename);
            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body    = $data['message'];
            $mail->send();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}

function sendMail($key, array $data, $user)
{

    $general = GeneralSetting::first();

    $template =  EmailTemplate::where('name', $key)->first();



    $message = variableReplacer('{username}', $user->username, $template->template);
    $message = variableReplacer('{sent_from}', @$general->sitename, $message);

    foreach ($data as $key => $value) {
        $message = variableReplacer("{" . $key . "}", $value, $message);
    }

    if ($general->email_method == 'php') {
        $headers = "From: $general->sitename <$general->site_email> \r\n";
        $headers .= "Reply-To: $general->sitename <$general->site_email> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        @mail($user->email, $template->subject, $message, $headers);
    } else {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $general->email_config->smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $general->email_config->smtp_username;
            $mail->Password   = $general->email_config->smtp_password;
            if ($general->email_config->smtp_encryption == 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port       = $general->email_config->smtp_port;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($general->site_email, $general->sitename);
            $mail->addAddress($user->email, $user->username);
            $mail->addReplyTo($general->site_email, $general->sitename);
            $mail->isHTML(true);
            $mail->Subject = $template->subject;
            $mail->Body    = $message;
            $mail->send();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
function content($key)
{
    $general = GeneralSetting::first();

    return SectionData::where('theme', $general->theme)->where('key', $key)->first();
}

function element($key, $take = 10)
{
    $general = GeneralSetting::first();

    return SectionData::where('theme', $general->theme)->where('key', $key)->take($take)->get();
}

function template()
{
    $general = GeneralSetting::first();

    if ($general->theme == 1) {
        return 'frontend.';
    } else {
        return "theme{$general->theme}.";
    }
}

function sectionManager()
{
    $general = GeneralSetting::first();

    if ($general->theme == 1) {

        return resource_path('views/') . 'sections.json';
    } else {
        return resource_path('views/theme' . $general->theme . '/') . 'sections.json';
    }
}

function colorText($haystack, $needle)
{
    $replace = "<span>{$needle}</span>";

    return str_replace($needle, $replace, $haystack);
}

function refferMoney($user2_id, $user1, $refferal_type, $amount, $plan_id)
{
    if (!$user1) {
        Log::error('User object is null, cannot proceed with referral commission distribution.', [
            'refferal_type' => $refferal_type,
            'amount' => $amount,
            'plan_id' => $plan_id
        ]);
        return;

    }

    // Start logging the beginning of the process
    Log::info('Starting referral commission distribution.', [
        'user1_id' => $user1->id,
        'refferal_type' => $refferal_type,
        'amount' => $amount,
        'plan_id' => $plan_id
    ]);

    // Retrieve the referred user
    $referredUser = User::find($user2_id);

    if (!$referredUser) {
        Log::error('Referred user not found.', [
            'user2_id' => $user2_id
        ]);
        return;
    }

    // Get the designations and levels
    $user2Designation = $referredUser->currentDesignation;
    $user2Level = $user2Designation->commission_level ?? 0;

    Log::info('User 2 designation and level.', [
        'user2_id' => $referredUser->id,
        'designation_id' => $user2Designation->id ?? null,
        'user2_level' => $user2Level
    ]);

    $user1Designation = $user1->currentDesignation;
    $user1Level = $user1Designation->commission_level ?? 0;

    Log::info('User 1 designation and level.', [
        'user1_id' => $user1->id,
        'designation_id' => $user1Designation->id ?? null,
        'user1_level' => $user1Level
    ]);

    // Retrieve the referral data for the given plan and type
    $referral = Refferal::where('plan_id', $plan_id)
                        ->where('type', $refferal_type)
                        ->first();

    if (!$referral) {
        Log::error('Referral data not found for the given plan and type.', [
            'plan_id' => $plan_id,
            'type' => $refferal_type
        ]);
        return;
    }

    // Assume $referral->level and $referral->commision are already arrays
    $referralLevels = $referral->level;
    $referralCommissions = $referral->commision;

    if (count($referralLevels) !== count($referralCommissions)) {
        Log::error('Mismatch between levels and commission data in the referral record.', [
            'referral_id' => $referral->id
        ]);
        return;
    }

    Log::info('Retrieved referral levels and commissions.', [
        'referral_id' => $referral->id,
        'levels_count' => count($referralLevels)
    ]);

    // Iterate through each level and commission
    foreach ($referralLevels as $index => $levelName) {
        $commissionLevel = $index + 1; // Assuming levels are ordered by index
        $commissionPercentage = $referralCommissions[$index];

        Log::info('Processing commission level.', [
            'commission_level' => $commissionLevel,
            'commission_percentage' => $commissionPercentage
        ]);

        if ($user2Level >= $commissionLevel) {
            // User 2 gets the commission for levels they are eligible for
            $commission = ($commissionPercentage * $amount) / 100;

            $referredUser->balance += $commission;
            $referredUser->save();

            RefferedCommission::create([
                'reffered_by' => $referredUser->id,
                'reffered_to' => $user1->id,
                'commission_from' => $referredUser->id,
                'amount' => $commission,
                'purpouse' => $refferal_type === 'invest' ? 'Return invest commission' : 'Return Interest Commission'
            ]);

            Log::info('Commission distributed to User 2.', [
                'user2_id' => $referredUser->id,
                'commission_amount' => $commission,
                'level' => $commissionLevel
            ]);
        } elseif ($user2Level < $commissionLevel && $user1Level >= $commissionLevel) {
            // User 1 gets the commission for the remaining levels
            $commission = ($commissionPercentage * $amount) / 100;

            $user1->balance += $commission;
            $user1->save();

            RefferedCommission::create([
                'reffered_by' => $user1->id,
                'reffered_to' => $referredUser->id,
                'commission_from' => $referredUser->id,
                'amount' => $commission,
                'purpouse' => $refferal_type === 'invest' ? 'Return invest commission' : 'Return Interest Commission'
            ]);

            Log::info('Commission distributed to User 1.', [
                'user1_id' => $user1->id,
                'commission_amount' => $commission,
                'level' => $commissionLevel
            ]);
        } else {
            Log::info('No commission distributed for this level.', [
                'commission_level' => $commissionLevel,
                'user2_level' => $user2Level,
                'user1_level' => $user1Level
            ]);
        }
    }

    Log::info('Referral commission distribution completed.', [
        'user1_id' => $user1->id,
        'user2_id' => $referredUser->id
    ]);
}




















function advertisements($size)
{
    $ad = Advertise::where('resolution', $size)->where('status', 1)->inRandomOrder()->first();
    if (!empty($ad)) {
        if ($ad->type == 1) {
            return  '<a  target="_blank" href="' . $ad->redirect_url . '"><img src="' . asset('asset/images/advertisement/' . $ad->ad_image) . '" alt="image" class="w-100"></a>';
        }
        if ($ad->type == 2) {
            return $ad->script;
        }
    } else {
        return '';
    }
}


function singleMenu($routeName)
{
    $class = 'active';

    if (request()->routeIs($routeName)) {
        return $class;
    }

    return '';
}

function arrayMenu($routeName)
{
    $class = 'open';
    if (is_array($routeName)) {
        foreach ($routeName as $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    }
}

function filterByVariousType(array $inputs)
{

    $generateHtml = '';

    foreach ($inputs as $key => $input) {

        if ($key === 'model') {

            $generateHtml .= <<<EOD
             <input type="hidden" name="model" class="form-control" value="{$input}" id="model">
            EOD;
        } elseif ($key === 'text') {

            $generateHtml .= <<<EOD
             <input type="text" name="{$input['name']}" placeholder="{$input['placeholder']}" class="form-control w-auto mr-3" id="{$input['id']}" data-colum="{$input['filter_colum']}">
            EOD;
        } elseif ($key === 'date') {
            $generateHtml .= <<<EOD
            <input type="date" name="{$input['name']}" class="form-control w-auto" id="{$input['id']}" data-colum="{$input['filter_colum']}">
           EOD;
        } elseif ($key === 'select') {
            $options = '';

            foreach ($input['options'] as $key => $option) {

                $options .= "<option value=" . $key . ">" . $option . " </option>";
            }

            $generateHtml .= <<<EOD
            <select type="date" name="{$input['name']}" class="form-control w-auto" id="{$input['id']}" data-colum="{$input['filter_colum']}">
               {$options}
            </select>
           EOD;
        }
    }


    return $generateHtml;
}


function currentPlan($user)
{
    $plan = Payment::with('plan')->where('user_id', $user->id)->where('payment_status', 1)->latest()->first();

    return $plan ? $plan->plan->plan_name : 'N/A';
}

function numberToWord($num = '')
{
    $num    = ( string ) ( ( int ) $num );

    if( ( int ) ( $num ) && ctype_digit( $num ) )
    {
        $words  = array( );

        $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );

        $list1  = array('','one','two','three','four','five','six','seven',
            'eight','nine','ten','eleven','twelve','thirteen','fourteen',
            'fifteen','sixteen','seventeen','eighteen','nineteen');

        $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
            'seventy','eighty','ninety','hundred');

        $list3  = array('','thousand','million','billion','trillion',
            'quadrillion','quintillion','sextillion','septillion',
            'octillion','nonillion','decillion','undecillion',
            'duodecillion','tredecillion','quattuordecillion',
            'quindecillion','sexdecillion','septendecillion',
            'octodecillion','novemdecillion','vigintillion');

        $num_length = strlen( $num );
        $levels = ( int ) ( ( $num_length + 2 ) / 3 );
        $max_length = $levels * 3;
        $num    = substr( '00'.$num , -$max_length );
        $num_levels = str_split( $num , 3 );

        foreach( $num_levels as $num_part )
        {
            $levels--;
            $hundreds   = ( int ) ( $num_part / 100 );
            $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
            $tens       = ( int ) ( $num_part % 100 );
            $singles    = '';

            if( $tens < 20 ) { $tens = ( $tens ? ' ' . $list1[$tens] . ' ' : '' ); } else { $tens = ( int ) ( $tens / 10 ); $tens = ' ' . $list2[$tens] . ' '; $singles = ( int ) ( $num_part % 10 ); $singles = ' ' . $list1[$singles] . ' '; } $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' ); } $commas = count( $words ); if( $commas > 1 )
        {
            $commas = $commas - 1;
        }

        $words  = implode( ', ' , $words );

        $words  = trim( str_replace( ' ,' , ',' , ucwords( $words ) )  , ', ' );
        if( $commas )
        {
            $words  = str_replace( ',' , ' and' , $words );
        }

        return $words;
    }
    else if( ! ( ( int ) $num ) )
    {
        return 'Zero';
    }
    return '';
}


function activeMenu($route)
{
    if(is_array($route)){
        if(in_array(url()->current(),$route)){
            return 'active';
        }
    }
    if($route == url()->current()){
        return 'active';
    }
}
