<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photos extends Model
{
	protected $table = 'photos';
	
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}