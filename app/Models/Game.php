<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MarcReichel\IGDBLaravel\Enums\Game\Category;

class Game extends Model
{
    public static array $_NOT_USE_PLATFORMS = [15, 16, 23, 25, 26, 27, 29, 30, 32, 35, 42, 44, 50, 51, 52, 53, 56, 57, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 77, 78, 79, 80, 82, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 131, 132, 133, 134, 135, 136, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 128, 161, 164, 166];
    public static array $_USE_ONLY_CATEGORIES = [Category::MAIN_GAME->value, Category::REMAKE->value, Category::REMASTER->value];

    protected $guarded = [];
}
