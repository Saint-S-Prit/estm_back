<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateEntity
{
    function update($request, string $fileName)
    {
        //contenu brut
        $raw = $request->getContent();
        //chaine qui représentera les segments du tableau qui sera obtenu après l'éclatement de $raw
        $delimiter = "multipart/form-data; boundary=";
        //chaine de caractère récurente présente dans $raw et qu'il faudra éliminer afin de pouvoir récupérer les données
        $boundary = "--" . explode($delimiter, $request->headers->get("content-type"))[1];
        //remplacement de tous les éléments énumérer dans le tableau constituant le 1er paramètre de str_replace par une chaine vide afin d'éliminer toutes informations dont on aura pas besoin de $raw
        //$elements devient la simplification de $raw
        $elements = str_replace([$boundary, "Content-Disposition: form-data;", "name="], "", $raw);
        //eclatements de $elements par '\r\n\r\n' afin d'obtenir un tableau qui nous permettra d'obtenir les données passées dans la requête
        $elementsTab = explode("\r\n\r\n", $elements);
        $data = [];
        //parcours de $elements par saut de 2 indexs (les index pairs représentent les clés et les impairs représentent les valeurs)
        for ($i = 0; isset($elementsTab[$i + 1]); $i += 2) {
            //clé
            $key = str_replace(["\r\n", ' "', '"'], '', $elementsTab[$i]);
            //si on tombe sur le fichier passé dans la requête
            if (strchr($key, $fileName)) {
                // ouvrir un fichier en mémoire
                $stream = fopen('php://memory', 'r+');
                // ecrire le binaire encodé en base64 dans le fichier
                fwrite($stream, $elementsTab[$i + 1]);
                //replacer le pointeur en début de fichier
                rewind($stream);
                //sauvegarde du fichier(blob) dans le tableau déclarer dans la boucle
                $data[$fileName] =  $stream;
            } else {
                //remplacer par une chaine "\r\n", "--" si la donnée passée en contient
                $val = str_replace(["\r\n", "--"], '', $elementsTab[$i + 1]);
                //stocker la données dans le tableau déclarer avant la boucle
                $data[$key] =  $val;
            }
        }
        //$data = tableau des données passées dans la requête

        // dd($data);
        return $data;
    }
}
