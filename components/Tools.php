<?php

namespace app\components;

class Tools {

    public static $screen = 0;

    public static function getTextInBrackets($str)
    {
        $startBracket = strpos($str, '(');
        $endBracket = strpos($str, ')');
        $lenght = ($endBracket - $startBracket);

        return substr($str, $startBracket+1, $lenght-1);
    }

    public static function ru2lat($str)
    {
        $tr = array(
            "А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g", "Д"=>"d",
            "Е"=>"e", "Ё"=>"yo", "Ж"=>"zh", "З"=>"z", "И"=>"i", 
            "Й"=>"j", "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n", 
            "О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s", "Т"=>"t", 
            "У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"ts", "Ч"=>"ch", 
            "Ш"=>"sh", "Щ"=>"sch", "Ъ"=>"", "Ы"=>"y", "Ь"=>"", 
            "Э"=>"e", "Ю"=>"yu", "Я"=>"ya", "а"=>"a", "б"=>"b", 
            "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ё"=>"yo", 
            "ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"j", "к"=>"k", 
            "л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p", 
            "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f", 
            "х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch", 
            "ъ"=>"", "ы"=>"y", "ь"=>"", "э"=>"e", "ю"=>"yu", 
            "я"=>"ya", " "=>"-", "."=>"", ","=>"", "/"=>"-",  
            ":"=>"", ";"=>"","—"=>"", "–"=>"-"
        );
        //print_r($str);
        return strtr($str,$tr);
    }

    public static function trimArray($data) {

        if (gettype($data) == 'array') {
            return array_map(array(__CLASS__, "trimArray"), $data);
        } else {
            return trim($data);
        }
    }

    public static function removeLastWordArray($data) {

        if (gettype($data) == 'array') {
            return array_map(array(__CLASS__, "removeLastWordArray"), $data);
        } else {
            if (substr_count($data, ' ') > 0) {
                //echo $data . ' - ' . substr_count($data, ' ') . ', ';
                $lastSpacePosition = strrpos($data, ' ');
                $textWithoutLastWord = substr($data, 0, $lastSpacePosition);
                return $textWithoutLastWord;
            } else {
                return $data;
            }
        }
    }

    public static function getLastWord($str, $delimitier = " ") {

        $str = htmlentities($str, null, 'utf-8');
        $str = str_replace("&nbsp;", " ", $str);

        $last_word_start = strrpos ( $str , $delimitier) + 1;
        $last_word_end = strlen($str) - 1;
        $last_word = substr($str, $last_word_start, $last_word_end);
        return $last_word;
    }

    public static function getFirstWord($str, $delimitier = " ") {

        $str = htmlentities($str, null, 'utf-8');
        $str = str_replace("&nbsp;", " ", $str);

        $first_word_start = 0;
        $first_word_end = strpos ( $str , $delimitier);
        $first_word = substr($str, $first_word_start, $first_word_end);
        return $first_word;
    }

    public static function model_exists($className) {
        $modelFolder = Yii::app()->params['configurationName'];
        return file_exists(Yii::getPathOfAlias('application.models.'.$modelFolder).DIRECTORY_SEPARATOR.$className.'.php');
    }

    public static function table_exists($tableName) {
        if (Yii::app()->db->schema->getTable($tableName)){
            return true;
        } else {
            return false;
        }
    }

    public static function getInBetweenStrings($start, $end, $str){
        $matches = array();
        $regex = "/$start([a-zA-Z0-9>_-]*)$end/";
        preg_match_all($regex, $str, $matches);
        return $matches[1];
    }

    public static function textBetweenChars ($start_limiter = '[', $end_limiter = ']', $haystack = "[test]") {

        # Step 1. Find the start limiter's position

        $start_pos = strpos($haystack,$start_limiter);
        if ($start_pos === FALSE)
        {
            die("Starting limiter ".$start_limiter." not found in ".$haystack);
        }

        # Step 2. Find the ending limiters position, relative to the start position

        $end_pos = strpos($haystack,$end_limiter,$start_pos);

        if ($end_pos === FALSE)
        {
            die("Ending limiter ".$end_limiter." not found in ".$haystack);
        }

        # Step 3. Extract the string between the starting position and ending position
        # Our starting is the position of the start limiter. To find the string we must take
        # the ending position of our end limiter and subtract that from the start limiter
        # -- thus giving us the length of our needle.
        # We must add 1 to the start position, since it includes our limiter, and we must subtract 1 from the end position

        $needle = substr($haystack, $start_pos+1, ($end_pos-1)-$start_pos);

        return $needle;

        //echo "Found $needle between $start_limiter and $end_limiter in $haystack";
    }

    /**
     * Determines if the browser provided a valid SSL client certificate
     *
     * @return boolean True if the client cert is there and is valid
     */
    public static function hasValidCert()
    {
        if (!isset($_SERVER['SSL_CLIENT_M_SERIAL'])
            || !isset($_SERVER['SSL_CLIENT_V_END'])
            || !isset($_SERVER['SSL_CLIENT_VERIFY'])
            || $_SERVER['SSL_CLIENT_VERIFY'] !== 'SUCCESS'
            || !isset($_SERVER['SSL_CLIENT_I_DN'])
        ) {
            return false;
        }
 
        if ($_SERVER['SSL_CLIENT_V_REMAIN'] <= 0) {
            return false;
        }
 
        return true;
    }

    //запускает скриншотилку веб-страницы с параметрами
    public static function makeDelayedPageScreenShoot($url, $outputFileName) {
		//return;
		//sleep(1);
		//xvfb-run --server-args="-screen 0  1024x768x24" -a /var/www/ggk/3rd/webPageScreenshooterLinux/webPageScreenshooter url=google.ru output=/tmp/goo.png
        $screenshooterFileName = '/var/www/ggk/3rd/webPageScreenshooterLinux/webPageScreenshooter';

        $cmd = 'xvfb-run --server-args="-screen ' . Tools::$screen . ' 1024x768x24" -a ' . $screenshooterFileName . ' url=' . $url . ' output=' . $outputFileName . '';
        Tools::$screen++;

        exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputFileName.'.log', $outputFileName.'.pid'));

        //echo $output;
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function print_array($title,$array) {

        if(is_array($array)){

            echo $title.". Count: ". count($array) .'<br/>'.
                "<pre>";
            print_r($array);
            echo "</pre>".
                "END ".$title."<br/>";
        }else{
            echo $title." is not an array.";
        }
    }

    public static function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public static function endsWithCut($haystack, $needle)
    {
        $length = strlen($needle);
        $lengthStr = strlen($haystack);
        if ($length == 0) {
            return true;
        }

        if (substr($haystack, -$length) === $needle) {
            return substr($haystack, 0, $lengthStr-$length);
        }

    }

    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public static function zipDirectory($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

    public static function zipDirectory1($directory, $zipFile) {
        // Initialize archive object
        $zip = new ZipArchive;
        $zip->open($zipFile, ZipArchive::CREATE);

        // Initialize empty "delete list"
        $filesToDelete = array();

        // Create recursive directory iterator
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Get real path for current file
            $filePath = $file->getRealPath();

            // Add current file to archive
            $zip->addFile($filePath);

            // Add current file to "delete list" (if need)
            if ($file->getFilename() != 'important.txt') 
            {
                $filesToDelete[] = $filePath;
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }

    public static function unzipFile($fileName, $extractTo) {
        $zip = new ZipArchive;
        $res = $zip->open($fileName);
        if ($res === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();
            return true;
        } else {
          Yii::app()->user->setFlash('error','unzip =( ' . $fileName . ' - ' . $extractTo);
        }
    }

    public static function checkInternetConnection()
    {
        $conn = @fsockopen("www.google.com", 80);
        if($conn)
        {
            fclose($conn);
            return TRUE;
        }
        
        return FALSE;
    }


    public static function downloadByProxy($url) {
        $proxy = '';
        //$proxyauth = 'user:password';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        if ($proxy > '') {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);

        return $curl_scraped_page;
    }

    public static function testProxyList() 
    {
        $fileName = Yii::getPathOfAlias('webroot') . '/proxy.txt';
        $outFileName = Yii::getPathOfAlias('webroot.assets') . '/bune.txt';
        $wrongFileName = Yii::getPathOfAlias('webroot.assets') . '/wrong.txt';

        $fileTime = filemtime($fileName);
        $dS = new DeltaRussian();
        $delta = $dS->getDelta($fileTime, time());

        if ($delta['mday'] > 3 || !file_exists($outFileName)) {
            $fisier = file_get_contents($fileName); // Read the file with the proxy list
            $linii = explode("\n", $fisier); // Get each proxy
            $fisier = fopen($outFileName, 'a'); // Here we will write the good ones
            //echo  $fisier;
            //Yii::app()->end();
            $wrongProxies = array();
            if (file_exists ( $wrongFileName )) {
                $wrongProxies = explode("\n", file_get_contents($wrongFileName));
            }
            //$wrongProxies = explode("\n", file_get_contents($wrongFileName));

            function testProxy($proxy, $fisier, $wrongProxies)
            {
                $splited = explode(':',$proxy); // Separate IP and port
                //if($con = @fsockopen($splited[0], $splited[1], $eroare, $eroare_str, 3))
                //{
                    if (!in_array($proxy, $wrongProxies)) {
                        //echo $proxy;
                        fwrite($fisier, $proxy . "\n"); // Check if we can connect to that IP and port
                        //print $proxy . '<br>'; // Show the proxy
                        //fclose($con); // Close the socket handle
                    }
                //}
            }

            for($i = 0; $i < count($linii) - 1; $i++) testProxy($linii[$i], $fisier, $wrongProxies); // Test each proxy

            fclose($fisier); // Close the file
        }
    }

    public static function getRandomProxy($dummy = false) 
    {
        if ($dummy) {
            return "0.0.0.0:0\n";
        } else {
            $wrongProxiesFilePath = Yii::getPathOfAlias('webroot.assets') . '/wrong.txt';
            $wrongProxies = array();
            if (file_exists ( $wrongProxiesFilePath )) {
                $wrongProxies = explode("\n", file_get_contents($wrongProxiesFilePath));
            }
            srand ((double)microtime()*1000000);
            $f_contentsFilePath = Yii::getPathOfAlias('webroot.assets') . '/bune.txt';

            if (file_exists ( $f_contentsFilePath )) {
                //echo '2'; Yii::app()->end();
                $f_contents = file ($f_contentsFilePath);
            } else {
                //echo '1'; Yii::app()->end();
                Tools::testProxyList();
                $f_contents = file ($f_contentsFilePath);
            }
            $line = $f_contents[array_rand ($f_contents)];
            while (in_array($line, $wrongProxies)) {
                $line = $f_contents[array_rand ($f_contents)];
            }
            return $line;
        }
    }

    public static function addWrongProxy($proxy) 
    {
        //srand ((double)microtime()*1000000);
        file_put_contents(Yii::getPathOfAlias('webroot.assets') . '/wrong.txt', $proxy, FILE_APPEND);
        //$line = $f_contents[array_rand ($f_contents)];
        //return $line;
    }

    public static function sendNotifyToUser($model, $notifyType, $fieldNames = array())
    {
        function getFieldsValues($model, $fieldNames) {
            $content = 'Информация по значениям полей: <br/>';
            foreach ($fieldNames as $key => $value) {
                    if (isset($model->{$value})) {
                    if (Tools::endsWith($value, 'Id')) {
                        $content .= ' - ' . Yii::t('app', $model->getAttributeLabel($value)) . ' = ' . $model->getRelatedReprValue($value) . '<br/>';
                    } else {
                        $content .= ' - ' . Yii::t('app', $model->getAttributeLabel($value)) . ' = ' . $model->{$value} . '<br/>';
                    }
                }
            }
            return $content;
        }

        if ($model->tableName() == 'itTikets') {
            $fieldNames = array('itTiketsName', 'itTaskTypeId', 'tiketDescription', 'itTiketStatusId');
        }

        if (!NActiveRecord::checkIsColumnExists('userId', $model->tableName()) || ($model->userId == 0)) {
            throw new CHttpException(500,'Не указан пользователь в модели ' . get_class($model) . ' записи ' .$model->id);
        }

        $subject = '';
        $content = '';
        // $email = Yii::app()->db->createCommand()
        // ->select('email')
        // ->from('profile u')
        // ->where('u.user_id=:user_id', array(':user_id'=> $model->userId))->queryScalar();
        $firstname = Yii::app()->db->createCommand()
                ->select('firstname')
                ->from('profile u')
                ->where('u.user_id=:user_id', array(':user_id'=> $model->userId))->queryScalar();

        switch($notifyType) {
            case 'create':
                $subject = 'Новая запись для Вас в таблице ' . Yii::t('app', $model->admin->pluralName);
                $content = 'Здравствуйте, ' . $firstname . '. <br/>Просьба ознакомиться с новой записью по адресу ' . Yii::app()->getBaseUrl(true) . Yii::app()->createUrl(get_class($model).'/view/id/'.$model->id) . '<br/><br/>';
                $content .= getFieldsValues($model, $fieldNames);
                $content .= '<br/> Благодарим за внимание. <br/>Если данное сообщение доставлено  к Вам по ошибке, просьба сообщить об этом Вашему системному администратору.';
                break;
            case 'update':
                $subject = 'Запись №'.$model->id.' в таблице ' . Yii::t('app', $model->admin->pluralName) . ' обновлена';
                $content = 'Здравствуйте, ' . $firstname . '. <br/>Просьба ознакомиться с актуализированной записью по адресу ' . Yii::app()->getBaseUrl(true) . Yii::app()->createUrl(get_class($model).'/view/id/'.$model->id) . '<br/><br/>';
                $content .= getFieldsValues($model, $fieldNames);
                $content .= '<br/> Благодарим за внимание. <br/>Если данное сообщение доставлено  к Вам по ошибке, просьба сообщить об этом Вашему системному администратору.';
                break;
            default:
                break;
        }

        Tools::sendMail($model->userId, $subject, $content, false, true);
    }

    public static function sendMail($to=0, $subject='', $content='', $toAdmin=false, $showFlash = false)
    {
        $settings = new Settings();
        $strOfSettings = $settings->getMetaDataValue('mailSettings');
        if (!empty($strOfSettings)) {
            $strToArr = explode(',',$strOfSettings);
            $arrOfSettings = array();

            foreach ($strToArr as $element){
                $arrayOfItems = explode('=',$element);
                $arrOfSettings[$arrayOfItems[0]] = $arrayOfItems[1];
            }

            $mail = new JPhpMailer;
            // настройки для почты
            $mail->IsSMTP();
            $mail->Host = $arrOfSettings['smtpHost'];
            $mail->SMTPAuth = ($arrOfSettings['smtpAuth'] == 1) ? true : false;
            $mail->SMTPSecure = '';
            $mail->SMTPDebug = 1;
            $mail->Port =  $arrOfSettings['smtpPort'];
            $mail->CharSet = empty($arrOfSettings['charset']) ? 'utf-8' : $arrOfSettings['charset'];
            $mail->Username = $arrOfSettings['username'];
            $mail->Password = $arrOfSettings['password'];
            // данные для отправки

            if ($toAdmin == false){
                if (is_numeric($to)){
                    $userProfile = YumUser::model()->findByPk($to)->profile;
                    if ($userProfile) {
                        $userEmail = $userProfile->attributes['email'];
                        $userName = $userProfile->attributes['firstname'] .' '. $userProfile->attributes['lastname'];
                        if (!empty($userEmail)){
                            $mail->SetFrom($arrOfSettings['username'], Yii::app()->name);
                            $mail->Subject = htmlspecialchars($subject);
                            $mail->MsgHTML($content);
                            $mail->AddAddress($userEmail, $userName);
                        } else {
                            $mail->SetFrom($arrOfSettings['username'], Yii::app()->name);
                            $mail->Subject = htmlspecialchars('Настройка профиля пользователю '.Yii::app()->user->loggedInAs());
                            $mail->MsgHTML('<p>В профиле пользователя '.Yii::app()->user->loggedInAs().' отсутствует адрес электронной почты,
                                в следствии чего он не может получать уведомления от системы.</p>');
                            $mail->AddAddress($arrOfSettings['adminEmail'], 'Администратор системы');
                        }
                    } else {
                        $mail->SetFrom($arrOfSettings['username'], Yii::app()->name);
                        $mail->Subject = htmlspecialchars('Создание профиля пользователю'.Yii::app()->user->loggedInAs());
                        $mail->MsgHTML('<p>Отсутствует профиль у пользователя '.Yii::app()->user->loggedInAs().',
                            в следствии чего он не может получать уведомления от системы на электронную почту.</p>');
                        $mail->AddAddress($arrOfSettings['adminEmail'], 'Администратор системы');
                    }
                } else {
                    throw new CException('Переданный ID пользователя не является числом, отправка пиьсма-уведомления провалилась.', 500);
                }
            } else {
                $mail->SetFrom($arrOfSettings['username'], Yii::app()->name);
                $mail->Subject = htmlspecialchars($subject);
                $mail->MsgHTML($content);
                $mail->AddAddress($arrOfSettings['adminEmail'], 'Администратор системы');
            }

            if ($mail->Send()) {
                if ($showFlash) { Yii::app()->user->setFlash("success", "Письмо-уведомление успешно отправлено."); }
            } else {
                if ($showFlash) { Yii::app()->user->setFlash("error", "Не удалось отправить письмо-уведомление."); }
            }
        }
    }

    public static function addComment ($modelName='', $modelId=0, $text='', $statusOfAgremment=1, $motionStatus=1) 
    {
        if (!empty($modelName) && !empty($modelId) && !empty($text)) {
            $userProfile = YumUser::model()->findByPk(Yii::app()->user->id)->profile;
            if ($userProfile) {
                $userName = $userProfile->attributes['firstname'] .' '. $userProfile->attributes['lastname'];
                $comment = str_replace("{username}", $userName, $text);
                Yii::app()->db->createCommand()
                    ->insert('zpAgreementComments', array(
                        'modelName'=>$modelName,
                        'idModel'=>$modelId,
                        'idUser'=>Yii::app()->user->id,
                        'zpStatusId'=>$statusOfAgremment ? $statusOfAgremment : 1,
                        'comment'=>$comment,
                        'zpMotionStatusesId'=>$motionStatus ? $motionStatus : 1,
                    ));
            } else {
                $subject = 'Создание профиля пользователю'.Yii::app()->user->loggedInAs();
                $content = '<p>Отсутствует профиль у пользователя '.Yii::app()->user->loggedInAs().',
                            в следствии чего он не может получать уведомления от системы на электронную почту.</p>';
                Tools::sendMail(0, $subject, $content, true);
            }
        }
    }

    public static function trim_all( $str , $what = NULL , $with = ' ' )
	{
		if( $what === NULL )
		{
			//	Character      Decimal      Use
			//	"\0"            0           Null Character
			//	"\t"            9           Tab
			//	"\n"           10           New line
			//	"\x0B"         11           Vertical Tab
			//	"\r"           13           New Line in Mac
			//	" "            32           Space
			
			$what	= "\\x00-\\x20";	//all white-spaces and control chars
		}
		
		return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
	}

	public static function ord_str($string) 
    { 
	    $encoded = "";
	    for ($n=0;$n<strlen($string);$n++){
	        $check = htmlentities($string[$n],ENT_QUOTES);
	       $string[$n] == $check ? $encoded .= "&#".ord($string[$n]).";" : $encoded .= $check;
	    }
	    return $encoded; 
	}

    public static function refreshControl() 
    {
        DTimer::log('datamining refreshControl for old/new rows');
        $model = new ZpIncomming();
        $existDate = $model->getMetaDataValue('refreshNewRecord');
        if ($existDate){
            if ($existDate < date("Y-m-d")){
                $dateOfLastIncommingRow = Yii::app()->db->createCommand('SELECT MAX(createTimestamp) FROM zpIncomming WHERE 1')->queryScalar();

                if ($dateOfLastIncommingRow) {
                    $interval = DateInterval::createFromDateString('1 day');
                    $todayDate = new DateTime(date("Y-m-d H:i:s"));
                    $lastDate = new DateTime($dateOfLastIncommingRow);
                    $diff = $lastDate->diff($todayDate);

                    $dayDiff = $diff->d;
                    $hourDiff = $diff->h;
                    $minuteDiff = $diff->i;

//                 если разница больше 1 дня, то шлем уведомление админу
                    if ($dayDiff >= $interval->d) {
                        $subject = 'Обновление данных в системе закупок';
                        $text = 'Нет новых входящих закупок в системе "Продажи". Текущая дата - ' . date("Y-m-d H:i:s") . ', дата самой старой записи в системе - ' .
                            $dateOfLastIncommingRow . '. Разница между последней записью и текущим временем составляет: ' . $dayDiff .
                            ' день ' . $hourDiff . ' часа(ов) ' . $minuteDiff . ' минут(у). Проверьте работу скрипта загрузки данных.';
                        Tools::sendMail(0, $subject, $text, true);
                        $model->setMetaDataValue('refreshNewRecord', date("Y-m-d"));
                        return true;
                    }
                }
            }
        } else {
            $model->setMetaDataValue('refreshNewRecord', date("Y-m-d"));
        }
    }
}

