<?php

namespace Modules\FileAndFolder\App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedFilesAndFolders extends Model
{
    protected $table = 'deleted_files_and_folders';

    protected $fillable = ['type', 'path', 'name'];
}
