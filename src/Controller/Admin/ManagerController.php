<?php
declare(strict_types=1);

namespace FileManager\Controller\Admin;

use Cake\Http\Exception\NotFoundException;
use Cake\I18n\DateTime;
use Exception;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToRetrieveMetadata;
use const UPLOAD_ERR_NO_FILE;

/**
 * @property \Search\Controller\Component\SearchComponent $Search
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class ManagerController extends AppController
{
    /**
     * Plugin page
     *
     * Displays the file manager
     *
     * @param string $path The directory path.
     * @return \Cake\Http\Response|void
     */
    public function index(string $path = '')
    {
        try {
            $content = $this->storage->listContents($path)->toArray();
        } catch (FilesystemException $e) {
            $this->Flash->error($e->getMessage());

            return $this->redirect(['action' => 'index', $path]);
        }

        $files = array_map(function ($item) {
            return [
                'type' => $item['type'],
                'name' => basename($item['path']),
                'extension' => pathinfo($item['path'], PATHINFO_EXTENSION),
                'path' => $item['path'],
                'size' => $item['type'] === 'file' ? $item['file_size'] : null,
                'modified' => DateTime::createFromTimestamp($item['last_modified'])->i18nFormat(),
                'perms' => $this->getPermissions($this->fullPath . $item['path']),
                'group' => $this->getGroup($this->fullPath . $item['path']),
                'owner' => $this->getOwner($this->fullPath . $item['path']),
            ];
        }, $content);

        // Sort by type (folders first, then files) and by name
        usort($files, function ($a, $b) {
            // Folders (type = 'dir') come before files (type = 'file')
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'dir' ? -1 : 1;
            }
            // If same type, sort alphabetically by name
            return strnatcasecmp($a['name'], $b['name']);
        });

        $this->set(compact('files', 'path'));
    }

    /**
     * Displays the file manager.
     *
     * @param string $path The directory path.
     * @return \Cake\Http\Response|void
     */
    public function view(string $path)
    {
        $file = null;
        try {
            if (!$this->storage->fileExists($path)) {
                return $this->redirect(['action' => 'index', dirname($path)]);
            }

            $file['name'] = basename($path);
            $file['url'] = $this->basePath . $path;
            $file['size'] = $this->storage->fileSize($path);
            $file['mime'] = $this->storage->mimeType($path);

            if ($this->isTextFile($path)) {
                $file['contents'] = nl2br(htmlspecialchars($this->storage->read($path)));
            }
        } catch (UnableToRetrieveMetadata $e) {
            if (!is_array($file)) {
                $file = [];
            }
            $file['mime'] = 'Unknown';
        } catch (FilesystemException $e) {
            $this->Flash->error($e->getMessage());
        }

        $this->set(compact('file', 'path'));
    }

    /**
     * Creates a new directory.
     *
     * @param string|null $path The base path to create the directory in.
     * @return \Cake\Http\Response|void
     */
    public function create(?string $path = null)
    {
        if ($this->request->is('post')) {
            $folderName = $this->request->getData('name');

            if (empty($folderName)) {
                $this->Flash->error(__('Folder name cannot be empty'));

                return $this->redirect(['action' => 'index', $path]);
            }

            try {
                $this->storage->createDirectory($path . '/' . $folderName);
                $this->Flash->success(__('The folder "{0}" has been created', $folderName));
            } catch (FilesystemException $e) {
                $this->Flash->error($e->getMessage());
            }

            return $this->redirect(['action' => 'index', $path]);
        }
    }

    /**
     * Removes a file or directory.
     *
     * @param string $path The file or directory path to remove.
     * @return \Cake\Http\Response|void
     */
    public function remove(string $path)
    {
        try {
            if ($this->storage->fileExists($path)) {
                $this->storage->delete($path);
                $this->Flash->success(__('File deleted successfully'));
            } elseif ($this->storage->directoryExists($path)) {
                $this->storage->deleteDirectory($path);
                $this->Flash->success(__('Folder deleted successfully'));
            } else {
                $this->Flash->error(__('File or directory not found.'));
            }
        } catch (FilesystemException $e) {
            $this->Flash->error($e->getMessage());
        }

        return $this->redirect(['action' => 'index', dirname($path)]);
    }

    /**
     * Renames a file or directory.
     *
     * @param string $path The current path of the file or directory.
     * @return \Cake\Http\Response|void
     */
    public function rename(string $path)
    {
        $name = basename($path);

        if ($this->request->is('post')) {
            $newName = $this->request->getData('name');
            if (empty($newName)) {
                $this->Flash->error(__('File name cannot be empty'));

                return $this->redirect(['action' => 'index', dirname($path)]);
            }

            $newPath = dirname($path) . '/' . $newName;

            try {
                $this->storage->move($path, $newPath);
                $this->Flash->success(__('"{0}" was renamed to "{1}"', $name, $newName));
            } catch (FilesystemException $e) {
                $this->Flash->error($e->getMessage());
            }

            return $this->redirect(['action' => 'index', dirname($path)]);
        }

        $this->set(compact('path', 'name'));
    }

    /**
     * Uploads files to a directory.
     *
     * @param string|null $path The path to upload files to.
     * @return \Cake\Http\Response|void
     */
    public function upload(?string $path = null)
    {
        $files = $this->request->getUploadedFiles()['files'] ?? [];

        if (empty($files) || (count($files) === 1 && $files[0]->getError() === UPLOAD_ERR_NO_FILE)) {
            $this->Flash->error(__('No file was uploaded.'));

            return $this->redirect(['action' => 'index', $path]);
        }

        if ($this->request->is('post')) {
            $success = true;
            $uploadedFiles = [];

            foreach ($files as $file) {
                try {
                    $filename = $file->getClientFilename();
                    $stream = $file->getStream()->detach();
                    $this->storage->writeStream($path . '/' . $filename, $stream);
                    $uploadedFiles[] = $filename;
                } catch (FilesystemException $e) {
                    $this->Flash->error(__('Failed to upload "{0}": {1}', $file->getClientFilename(), $e->getMessage()));
                    $success = false;
                    // Optionally: continue uploading other files or break here
                }
            }

            if ($success) {
                if (count($uploadedFiles) > 1) {
                    $this->Flash->success(__('The following files have been uploaded successfully: {0}', implode(', ', $uploadedFiles)));
                } elseif (!empty($uploadedFiles)) {
                    $this->Flash->success(__('"{0}" has been uploaded successfully.', $uploadedFiles[0]));
                }
            }

            return $this->redirect(['action' => 'index', $path]);
        }
    }

    /**
     * Downloads a file.
     *
     * @param string $path The path to the file to be downloaded.
     * @return \Cake\Http\Response|null
     */
    public function download(string $path)
    {
        try {
            return $this->response->withFile($this->fullPath . $path, ['download' => true]);
        } catch (NotFoundException $e) {
            $this->Flash->error($e->getMessage());
        }

        return $this->redirect(['action' => 'index', dirname($path)]);
    }

    /**
     * Sets the permissions of a file or folder.
     *
     * @param string $path The file or directory path.
     * @return \Cake\Http\Response|void
     */
    public function setPermissions(string $path)
    {
        if (!$this->storage->has($path)) {
            $this->Flash->error(__('File or directory not found.'));

            return $this->redirect(['action' => 'index', dirname($path)]);
        }

        if ($this->request->is('post')) {
            $permissions = $this->request->getData('permissions');

            // Convert permissions array to a numeric mode (e.g., 0755)
            $newPermissions = 0;

            // Owner permissions
            $newPermissions |= !empty($permissions['owner']['read']) ? 0400 : 0;
            $newPermissions |= !empty($permissions['owner']['write']) ? 0200 : 0;
            $newPermissions |= !empty($permissions['owner']['execute']) ? 0100 : 0;

            // Group permissions
            $newPermissions |= !empty($permissions['group']['read']) ? 0040 : 0;
            $newPermissions |= !empty($permissions['group']['write']) ? 0020 : 0;
            $newPermissions |= !empty($permissions['group']['execute']) ? 0010 : 0;

            // Others permissions
            $newPermissions |= !empty($permissions['others']['read']) ? 0004 : 0;
            $newPermissions |= !empty($permissions['others']['write']) ? 0002 : 0;
            $newPermissions |= !empty($permissions['others']['execute']) ? 0001 : 0;

            try {
                if (!chmod($this->fullPath . $path, $newPermissions)) {
                    throw new Exception(__('Failed to change permissions.'));
                }
                $this->Flash->success(__('Permissions updated successfully.'));
            } catch (Exception $e) {
                $this->Flash->error($e->getMessage());
            }

            return $this->redirect(['action' => 'index', dirname($path)]);
        }

        // Retrieve current permissions (works for both files and directories)
        $currentPermissions = fileperms($this->fullPath . $path);

        // Parse current permissions into an array for checkboxes
        $currentPerms = [
            'owner' => [
                'read' => $currentPermissions & 0400 ? true : false,
                'write' => $currentPermissions & 0200 ? true : false,
                'execute' => $currentPermissions & 0100 ? true : false,
            ],
            'group' => [
                'read' => $currentPermissions & 0040 ? true : false,
                'write' => $currentPermissions & 0020 ? true : false,
                'execute' => $currentPermissions & 0010 ? true : false,
            ],
            'others' => [
                'read' => $currentPermissions & 0004 ? true : false,
                'write' => $currentPermissions & 0002 ? true : false,
                'execute' => $currentPermissions & 0001 ? true : false,
            ],
        ];

        $this->set(compact('path', 'currentPerms'));
    }

    /**
     * Gets the permissions of a file.
     *
     * @param string $file The full file path.
     * @return string|null The file permissions in string format (e.g. -rw-r--r--), or null on failure.
     */
    private function getPermissions(string $file): ?string
    {
        // Note: File permissions may not work with Flysystem, especially with cloud storage
        $perms = fileperms($file); // Suppress errors for non-existent files
        if ($perms === false) {
            return null; // Indicate that permissions can't be retrieved
        }

        $info = '';
        switch ($perms & 0xF000) {
            case 0xC000:
                $info = 's';
                break;
            case 0xA000:
                $info = 'l';
                break;
            case 0x8000:
                $info = '-';
                break;
            case 0x6000:
                $info = 'b';
                break;
            case 0x4000:
                $info = 'd';
                break;
            case 0x2000:
                $info = 'c';
                break;
            case 0x1000:
                $info = 'p';
                break;
            default:
                $info = 'u'; // Unknown
        }

        // Owner
        $info .= ($perms & 0x0100 ? 'r' : '-');
        $info .= ($perms & 0x0080 ? 'w' : '-');
        $info .= ($perms & 0x0040 ? ($perms & 0x0800 ? 's' : 'x') : ($perms & 0x0800 ? 'S' : '-'));

        // Group
        $info .= ($perms & 0x0020 ? 'r' : '-');
        $info .= ($perms & 0x0010 ? 'w' : '-');
        $info .= ($perms & 0x0008 ? ($perms & 0x0400 ? 's' : 'x') : ($perms & 0x0400 ? 'S' : '-'));

        // World
        $info .= ($perms & 0x0004 ? 'r' : '-');
        $info .= ($perms & 0x0002 ? 'w' : '-');
        $info .= ($perms & 0x0001 ? ($perms & 0x0200 ? 't' : 'x') : ($perms & 0x0200 ? 'T' : '-'));

        return $info;
    }

    /**
     * Gets the group of a file.
     *
     * @param string $path The full file path.
     * @return string|null The group name or null if unavailable.
     */
    private function getGroup(string $path): ?string
    {
        if (!function_exists('posix_getgrgid')) {
            return null;
        }

        $groupId = filegroup($path);
        if (!is_int($groupId)) {
            return null;
        }

        $groupInfo = posix_getgrgid($groupId);

        return $groupInfo['name'] ?? null;
    }

    /**
     * Gets the owner of a file.
     *
     * @param string $path The full file path.
     * @return string|null The owner name or null if unavailable.
     */
    private function getOwner(string $path): ?string
    {
        if (!function_exists('posix_getpwuid')) {
            return null;
        }

        $ownerId = fileowner($path);
        if (!is_int($ownerId)) {
            return null;
        }

        $userInfo = posix_getpwuid($ownerId);

        return $userInfo['name'] ?? null;
    }

    /**
     * Checks if a file is a text file.
     *
     * @param string $filePath The file path.
     * @param int $sampleSize The number of bytes to sample.
     * @return bool True if the file is text, false otherwise.
     */
    private function isTextFile(string $filePath, int $sampleSize = 512): bool
    {
        try {
            // Read a sample of the file
            $sample = $this->storage->read($filePath);

            // If the file is smaller than the sample size, adjust the sample size
            if (strlen($sample) > $sampleSize) {
                $sample = substr($sample, 0, $sampleSize);
            }

            // If the file is empty, it's considered text
            if (strlen($sample) === 0) {
                return true;
            }

            // Check if the content is valid UTF-8
            if (mb_check_encoding($sample, 'UTF-8')) {
                return true;
            }

            // Check for binary characters (non-printable ASCII excluding common control characters)
            $textPattern = '/^[\x09\x0A\x0D\x20-\x7E\xA0-\xFF]*$/';

            return (bool)preg_match($textPattern, $sample);
        } catch (FilesystemException $e) {
            // Handle any errors (e.g., file not found, permission issues)
            return false;
        }
    }
}
