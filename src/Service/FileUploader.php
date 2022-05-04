<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
	private $slugger;

	private $targetDirectory_actu;

	private $targetDirectory_photo;

	public function __construct($targetDirectory_actu, $targetDirectory_photo, SluggerInterface $slugger)
	{
		$this->targetDirectory_actu = $targetDirectory_actu;
		$this->targetDirectory_photo = $targetDirectory_photo;
		$this->slugger = $slugger;
	}

	public function upload(UploadedFile $file, $directory = 'photo')
	{
		$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$safeFilename = $this->slugger->slug($originalFilename);
		$fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

		try {
			$directory == 'photo'
				? $file->move($this->targetDirectory_photo(), $fileName)
				: $file->move($this->targetDirectory_actu(), $fileName)
			;
		} catch (FileException $e) {
			// ... handle exception if something happens during file upload
			return null; // for example
		}
		return $fileName;
	}

	public function targetDirectory_actu()
	{
		return $this->targetDirectory_actu;
	}

	public function targetDirectory_photo()
	{
		return $this->targetDirectory_photo;
	}
}
