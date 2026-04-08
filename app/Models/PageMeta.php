<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageMeta extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'page_meta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['page_id', 'meta_key', 'meta_value'];

    /**
     * Get the page that owns the meta data.
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
