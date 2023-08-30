<?php

namespace App\Services\Notification;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Modules\Settings\Models\Setting;

final class SmsService implements NotificationInterface
{
    /**
     * User mobile numbers as collection array
     *
     * @var Collection
     */
    public $users = [];

    /**
     * SMS message content
     *
     * @var string
     */
    public string $message = '';

    /**
     * Error result
     *
     * @var boolean|string
     */
    public $error = false;

    public function receivers(Collection $users = null)
    {
        $this->users = $users;
        return $this;
    }

    public function setMessage(string $message) {
        $this->message = $message;
        return $this;
    }

    public function send(): bool
    {
        try {

            if ($this->users) {

                $params = [
                    'api_token' => Setting::getOption('sms_api_token', env('SMS_SSL_API_TOKEN')),
                    'sid' => Setting::getOption('sms_secret_id', env('SMS_SSL_SID')),
                    'sms' => $this->message,
                    'msisdn' => $this->users->implode(','),
                    'csms_id' => date('Ymd-') . rand(10000, 99999)
                ];

                // $params['sms'] = str_replace(["<br />", '<br>', '<br/>'], "", $params['sms']);

                $response = $this->callApi(Setting::getOption('sms_url', env('SMS_SSL_URL')), json_encode($params));

                if($response) {
                    $response = json_decode($response, true);
                }

                // $response = Http::acceptJson()
                //     ->withHeaders(['Content-Type: application/json'])
                //     ->get(setting_option('sms_url', env('SMS_SSL_URL')), $params);

                if(isset($response['status']) && strtolower($response['status']) == 'failed') {
                    throw new Exception($response['error_message']);
                }

            } else {
                throw new Exception('No mobile numbers found');
            }
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $this->error = [
                'message' => $responseBodyAsString
            ];
        } catch (Exception $e) {
            $this->error = [
                'message' => $e->getMessage(),
            ];
        }

        return $this->error ? false : true;
    }

    private function callApi($url, $params)
    {
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),
            'accept:application/json'
        ));

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
