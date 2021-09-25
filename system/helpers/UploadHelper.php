<?php

class UploadHelper{
    
    protected $path = '/sistema/public/fotos/', 
            $file, $filename, $fileTmpName, 
            $fileSize, $fileType,$binario,
            $caminho, $novonome;
    
    public function setPath($path){
        $this->path = $path;
        return $this;
    }
    
    public function setFile($file){
        $this->file = $file;
        $this->setFileName();
        $this->setFileTmpName();
        $this->setFileType();
        $this->setFileSize();
        $this->setBinario();
        $this->setCaminho();
        return $this;
    }
    
    protected function setFileName(){
        $this->filename = $this->file['name'];
    }
    
    protected function setFileTmpName(){
        $this->fileTmpName = $this->file['tmp_name'];
    }
    
    protected function setFileSize(){
        $this->fileSize = $this->file['size'];
    }
    
    protected function setFileType(){
        $this->fileType = $this->file['type'];
    }
    
    protected function setBinario(){
        $fp = fopen($this->fileTmpName, 'rb');
        $conteudo = fread($fp, $this->fileSize);
        $this->binario = addslashes($conteudo);
        fclose($fp);
    }
    
    protected function setCaminho(){
        $this->novonome = md5(uniqid(time())) . strrchr($this->getFileName(), ".");
        $this->caminho = $this->path . $this->novonome;
    }
    
    public function getCaminho(){
        return $this->caminho;
    }
    
    public function getFileType(){
        return $this->fileType;
    }

    public function getFileSize(){
        return $this->fileSize;
    }

    public function getBinario(){
        return $this->binario;
    }

    public function getNovoNome(){
        return $this->novonome;
    }
    
    public function getFileName(){
        return $this->filename;
    }
    
    public function upload(){
        
        if (move_uploaded_file($this->fileTmpName, $_SERVER["DOCUMENT_ROOT"] . $this->caminho)){
            return true;
        }  else {
            return false;
        }
    }
}

