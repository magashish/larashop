<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // For the inverse relationship

class OfferAttachment extends Model
{
    use HasFactory;

    // Specifies the table name if it doesn't follow Laravel's pluralization rules (e.g., if table is 'offer_attachments' and model is 'OfferAttachment')
    protected $table = 'offer_attachments';

    // Allow these fields to be mass-assignable
    protected $fillable = ['offer_id', 'file_image'];

    /**
     * Define the inverse relationship: an attachment belongs to an offer.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}