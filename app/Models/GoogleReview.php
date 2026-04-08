<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_name',
        'author_img',
        'platform_icon',
        'rating',
        'review_text',
        'type',
    ];

    /**
     * Optional: Casts for specific fields.
     * This ensures 'rating' is always treated as an integer.
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Helper method to display stars in Blade easily.
     * Usage in Blade: {!! $review->display_stars !!}
     */
    public function getDisplayStarsAttribute()
    {
        $html = '';
        $rating = $this->rating ?? 5; // Default to 5 if null

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                // If you use FontAwesome
                $html .= '<i class="fas fa-star" style="color: #ffc107;"></i>';
            } else {
                $html .= '<i class="far fa-star" style="color: #e4e5e9;"></i>';
            }
        }

        return $html;
    }
}