<?php

namespace App\Controllers;

use App\Models\Photos;
use Slim\Views\Twig as View;
use App\Models\User;

class PhotoController extends Controller
{
    private $file;
    private $image;
    private $file_type;
    private $user_id;

    public function update($request, $reesponse)
    {
        $this->file    = $request->getUploadedFiles();
        $this->user_id = (int)$request->getParam('user_id');
        $orig_photo_id = empty($request->getParam('id')) == false ? (int)$request->getParam('id') : null;
        $photo_id      = $this->storeFileIfExists($orig_photo_id);

        $sql = "
          UPDATE users SET
            photo_id = $photo_id
          WHERE id  = {$this->user_id}
        ";
        $stm = $this->db->prepare($sql);
        $update = $stm->execute();

        if ($update) {
            $this->convertToImage($photo_id);
            return json_encode([
                'success' => true,
            ]);
        } else {
            return json_encode([
                'success' => false,
                'message' => 'There was an error.',
            ]);
        }
    }

    //Warning...Bad Practice- Dont store Images in DB, just for this small app
    private function storeFileIfExists($orig_photo_id)
    {
        if (empty($this->file['image_file']->file)) { return null; }

        $this->file_type = mime_content_type($this->file['image_file']->file);
        $this->image = fopen($this->file['image_file']->file, 'rb');

        $inserted = $this->insertIfNotExists();
        if (!$inserted) {
            $this->updatePhoto();

            return $orig_photo_id;
        }
        $photo = Photos::where('user_id', $this->user_id)->first();

        return $photo->id;
    }

    private function insertIfNotExists()
    {
        $sql = "
          INSERT INTO photos
          (user_id, photo, file_type)
          VALUES (:user_id, :photo, :file_type)
        ";
        $stm = $this->db->prepare($sql);
        $stm->bindParam(':file_type', $this->file_type);
        $stm->bindParam(':user_id', $this->user_id);
        $stm->bindParam(':photo', $this->image, \PDO::PARAM_LOB);

        return $stm->execute();
    }

    private function updatePhoto()
    {
        $sql = "
           UPDATE photos SET
              photo = :photo,
              file_type = :file_type
           WHERE user_id = :user_id
        ";
        $stm = $this->db->prepare($sql);
        $stm->bindParam(':file_type', $this->file_type);
        $stm->bindParam(':user_id', $this->user_id);
        $stm->bindParam(':photo', $this->image, \PDO::PARAM_LOB);

        return $stm->execute();
    }

    private function convertToImage($id)
    {
        $sql = "
          SELECT 
            file_type,
            photo
           FROM photos
          WHERE id = :id";

        $stm = $this->db->prepare($sql);
        $stm->execute(array(":id" => $id));
        $stm->bindColumn(1, $file_type);
        $stm->bindColumn(2, $photo, \PDO::PARAM_LOB);

        $stm->fetch(\PDO::FETCH_BOUND);

        $photo = array("mime" => $file_type,
            "data" => $photo);

            header("Content-Type:" . $photo['mime']);
            echo $photo['data'];
            die;


        /*ob_start();
        fpassthru($v['logo']);
        $contents = ob_get_contents();
        ob_end_clean();
        $logo = "data:image/png;base64," . base64_encode($contents);
        return "<img height=\"auto\" width=\"42\" src='$logo' />";*/
    }
}