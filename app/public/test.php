<?php

    use App\Dao\BabyModelDao;
    use App\Models\BabyModel;

    require __DIR__ . '/../bootstrap.php';

    $baby = new BabyModel();
    $baby->name = 'Ricardo Cardozo Silveira';
    $baby->description = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.';
    $baby->gender = 'M';
    $baby->born_at = '2022-09-05';

    $b = BabyModelDao::create($baby);

    var_dump($b);
    
    

    