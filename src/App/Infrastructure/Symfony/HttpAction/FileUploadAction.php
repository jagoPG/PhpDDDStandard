<?php

/*
 * This file is part of the Php DDD Standard project.
 *
 * Copyright (c) 2017 LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\Symfony\HttpAction;

use BenGorFile\File\Application\Command\Upload\ByHashUploadFileCommand;
use BenGorFile\File\Domain\Model\FileAlreadyExistsException;
use BenGorFile\File\Domain\Model\FileDoesNotExistException;
use BenGorFile\File\Domain\Model\FileMimeTypeDoesNotSupportException;
use BenGorFile\File\Domain\Model\FileNameInvalidException;
use BenGorFile\File\Infrastructure\CommandBus\FileCommandBus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileUploadAction
{
    private $commandBus;
    private $twig;

    public function __construct(\Twig_Environment $twig, FileCommandBus $commandBus)
    {
        $this->twig = $twig;
        $this->commandBus = $commandBus;
    }

    public function __invoke(Request $request)
    {
        if (false === $request->files->has('file')) {
            throw new \InvalidArgumentException('Given file property is not in the request');
        }

        $success = false;
        $errorCode = '';
        $fileData = null;

        try {
            $fileData = $this->upload($request);
            $success = true;
        } catch (FileDoesNotExistException $exception) {
            $errorCode = 'FileDoesNotExistException';
        } catch (FileAlreadyExistsException $exception) {
            $errorCode = 'FileAlreadyExistsException';
        } catch (FileMimeTypeDoesNotSupportException $exception) {
            $errorCode = 'FileMimeTypeDoesNotSupportException';
        } catch (FileNameInvalidException $exception) {
            $errorCode = 'FileNameInvalidException';
        }

        return new Response(
            $this->twig->render('json/file.json.twig', [
                'fileData'  => $fileData,
                'success'   => $success,
                'errorCode' => $errorCode,
            ])
        );
    }

    private function upload(Request $request)
    {

        $uploadedFile = $request->files->get('file');

        $command = new ByHashUploadFileCommand(
            $uploadedFile->getClientOriginalName(),
            file_get_contents($uploadedFile->getPathname()),
            $uploadedFile->getMimeType()
        );
        $this->commandBus->handle($command);

        $request->files->remove('file');

        return [
            'id'       => $command->id(),
            'name'     => $uploadedFile->getClientOriginalName(),
            'mimeType' => $uploadedFile->getMimeType(),
        ];
    }
}