<?php

    namespace App\Services;

    use Exception;
    use SplFileInfo;

    class FileUploadService
    {
        /**
         * The slim-provided uploaded file
         *
         * @var Slim\Http\UploadedFile
         */
        private $uploadedFile;

        private $filename;

        public function __construct(\Psr\Http\Message\UploadedFileInterface $f) {
            $this->uploadedFile = $f;

            $extension = pathinfo($this->uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
            $this->filename = sprintf('%s.%0.8s', $basename, $extension);
        }

        public function setFilename($fname) {
            $extension = pathinfo($this->uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $this->filename = sprintf('%s.%s', $fname, $extension);
        }

        public function moveUploadedFileTo($pathToDirectory) {
            
            // verifica se o diretorio destino existe
            if (!file_exists($pathToDirectory)) {
                mkdir($pathToDirectory, 0755, true);
                chmod($pathToDirectory, 0755);
            }

            // $extension = pathinfo($this->uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            // $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
            // $filename = sprintf('%s.%0.8s', $basename, $extension);

            $this->uploadedFile->moveTo($pathToDirectory . DIRECTORY_SEPARATOR . $this->filename);

            return $this->filename;
        }

        public function validateFileMaxSize($maxSize) : bool {
            
            // arredondar o tamanho do arquivo enviado
            $size = ROUND($this->uploadedFile->getSize()/1024);           
            if ($size > $maxSize) {
                return false;
            }

            return true;
        }

        public function validateFileExtension(array $extensionList) : bool {

            if (!in_array($this->uploadedFile->getClientMediaType(),$extensionList)) {
                return false;
            }

            return true;
        }

    }