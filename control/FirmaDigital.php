<?php

//fRnk: Firma Digital Class
class FirmaDigital
{
    private $url = "https://localhost:9000/";

    function firmar($file_path_ori, $file_path_sig, $pin)
    {
        $endpoint_get_token = 'api/token/connected';
        $endpoint_post_pin = 'api/token/data';
        $endpoint_post_firmar_pdf = 'api/token/firmar_pdf';

        $client = curl_init();
        $headers = [
            'Content-type: application/json'
        ];
        curl_setopt($client, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($client, CURLOPT_AUTOREFERER, true);
        curl_setopt($client, CURLOPT_HEADER, false);
        curl_setopt($client, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($client, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($client, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($client, CURLOPT_URL, $this->url . $endpoint_get_token);
        curl_setopt($client, CURLOPT_CUSTOMREQUEST, "GET");
        $response = curl_exec($client);
        $data = json_decode($response);
        curl_close($client);
        if (!empty($data)) {
            if ($data->finalizado === true && count($data->datos->tokens) > 0) {
                curl_setopt($client, CURLOPT_URL, $this->url . $endpoint_post_pin);
                curl_setopt($client, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($client, CURLOPT_POST, true);
                $post = [
                    'slot' => $data->datos->tokens[0]->slot,
                    'pin' => $pin
                ];
                curl_setopt($client, CURLOPT_POSTFIELDS, json_encode($post));
                $response = curl_exec($client);
                $data2 = json_decode($response);
                curl_close($client);
                if ($data2->finalizado === true) {
                    $pdf = base64_encode(file_get_contents($file_path_ori));
                    if (strpos($pdf, 'data:application/pdf;base64,') !== false) {
                        $pdf = str_replace('data:application/pdf;base64,', '', $pdf);
                    }

                    $alias = $data2->datos->data_token->data[0]->alias;
                    $post = [
                        'slot' => $data->datos->tokens[0]->slot,
                        'pin' => $pin,
                        'alias' => $alias,
                        'pdf' => 'pdf_base64'
                    ];
                    $post = json_encode($post);
                    $post = str_replace('pdf_base64', $pdf, $post);

                    curl_setopt($client, CURLOPT_VERBOSE, true);
                    curl_setopt($client, CURLOPT_URL, $this->url . $endpoint_post_firmar_pdf);
                    curl_setopt($client, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($client, CURLOPT_POST, true);
                    curl_setopt($client, CURLOPT_POSTFIELDS, $post);
                    $response = curl_exec($client);
                    $data3 = json_decode($response);
                    curl_close($client);
                    if ($data3->finalizado === true) {
                        $pdf_firmado_base64 = $data3->datos->pdf_firmado;
                        if (!empty($pdf_firmado_base64)) {
                            $base64_data = base64_decode($pdf_firmado_base64, true);
                            file_put_contents($file_path_sig, $base64_data);
                            return ['code' => 200, 'msg' => 'Documento firmado exitosamente.'];
                        }
                    } else {
                        return ['code' => 500, 'msg' => 'El documento no se pudo firmar, intentelo nuevamente.'];
                    }
                } else {
                    return ['code' => 500, 'msg' => 'No se ha podido iniciar la sesión de firma de documentos, intentelo nuevamente.'];
                }
            } else {
                return ['code' => 500, 'msg' => 'No se ha detectado su TOKEN, conéctelo y vuelva a intentarlo nuevamente.'];
            }
        } else {
            return ['code' => 500, 'msg' => 'No se ha detectado su TOKEN, conéctelo, inicie Jacobitus Total y vuelva a intentarlo nuevamente.'];
        }
    }
}