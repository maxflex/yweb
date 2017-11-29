<?php
namespace App\Models\Service;

class Api {
		/**
		 * Отправить запрос.
		 *
		 */
		public static function exec($function, $data, $decode = false)
		{
            $data = (array)$data;

			// Добавляем API_KEY к запросу
			// $data["API_KEY"] = self::API_KEY;
			if ($function == 'AddRequest') {
				$data['id_google']  = static::_googleId();
                $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $data['referer']	= $_COOKIE['source']['referer'];
			    $data['referer_url']= $_COOKIE['source']['url'];
                $url = config('app.api-url');
			} else {
                $data['egecentr_source'] = 1;
                $url = config('app.api-egerep-url');
            }


			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url . $function);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
								http_build_query($data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$server_output = curl_exec($ch);

			curl_close($ch);

			return ($decode ? json_decode($server_output, true) : $server_output);
		}

        private static function _googleId()
        {
            if (! isset($_COOKIE['_ga'])) {
                return '';
            }
 			$parts = explode('.', $_COOKIE['_ga']);
 			return "{$parts[2]}.{$parts[3]}";
 		}
}

?>
