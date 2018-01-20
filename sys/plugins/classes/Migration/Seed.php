<?php
namespace App\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Phinx\Seed\AbstractSeed;
use App\Migration\Connect; // Trait

class Seed extends AbstractSeed {
  use Connect;

}