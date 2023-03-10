<?php
function drive($ruc, $nombrepadre, $nombrehijo)
{
    $ruc = $ruc;
    $nombrepadre = $nombrepadre;
    $nombrehijo = $nombrehijo;
    $idpadre = "-";
    $idhijo = "-";

    include 'api-google/vendor/autoload.php';

    putenv('GOOGLE_APPLICATION_CREDENTIALS=administrararchivos-372223-9e124c5e1002.json');

    $client = new Google_Client();

    $client->useApplicationDefaultCredentials();
    $client->setScopes(['https://www.googleapis.com/auth/drive']);

    $service = new Google_Service_Drive($client);

    $optParams = array(
        'q' => "name='$ruc'",
        'pageSize' => 10,
        'fields' => "files(id, name, size)",
    );

    $resultado = $service->files->listFiles(
        $optParams
    );

    if (count($resultado) > 0) {
        $optParams = array(
            'pageSize' => 10,
            'fields' => "nextPageToken, files(contentHints/thumbnail,fileExtension,iconLink,id,name,size,thumbnailLink,webContentLink,webViewLink,mimeType,parents)",
            'q' => " '" . $resultado[0]->id . "'  in parents",
        );

        $resultado = $service->files->listFiles(
            $optParams
        );

        foreach ($resultado as $data) {
            if ($data->name == $nombrepadre) {
                $idpadre = $data->id;
                $optParams = array(
                    'pageSize' => 10,
                    'fields' => "nextPageToken, files(contentHints/thumbnail,fileExtension,iconLink,id,name,size,thumbnailLink,webContentLink,webViewLink,mimeType,parents)",
                    'q' => "'" . $data->id . "' in parents",
                );

                $resultado = $service->files->listFiles(
                    $optParams
                );

                foreach ($resultado as $data) {
                    if ($data->name == $nombrehijo) {
                        $idhijo = $data->id;
                        $optParams = array(
                            'pageSize' => 10,
                            'fields' => "nextPageToken, files(contentHints/thumbnail,fileExtension,iconLink,id,name,size,thumbnailLink,webContentLink,webViewLink,mimeType,parents)",
                            'q' => "'" . $data->id . "' in parents",
                        );

                        $resultado = $service->files->listFiles(
                            $optParams
                        );
                        return array(
                            'cantidad' => count($resultado),
                            'idpadre' => $idpadre,
                            'idhijo' => $idhijo,
                        );
                    }
                }
            }
        }
    }
    return array(
        'cantidad' => 0,
        'idpadre' => $idpadre,
        'idhijo' => $idhijo,
    );

}
