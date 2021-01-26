<?php
require('simple_html_dom.php');

function get_data($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_URL, $url);

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function image_to_base64($url)
{
	$image = file_get_contents($url);
	if ($image !== false){
	    return 'data:image/jpg;base64,'.base64_encode($image);

	}
}

$artikels = [];
$results = [];
$counter = 1;
$base_url = 'http://arsip.borobudurpedia.id';
$url = $base_url.'/index.php/informationobject/browse?page='.$counter.'&view=table&onlyMedia=1&topLod=0&sort=alphabetic&sortDir=asc';
$count_artikel = str_get_html(get_data($url));
$counter = (int) $count_artikel->find('div[class=pagination pagination-centered] ul li[class=last] a',0)->title;
// $counter = 1;

for ($i=1; $i <= $counter ; $i++) { 
	$html = str_get_html(get_data($base_url.'/index.php/informationobject/browse?page='.$i.'&view=table&onlyMedia=1&topLod=0&sort=alphabetic&sortDir=asc'));

	$artikels =  $html->find('article[class="search-result has-preview"]');
	foreach ($artikels as $key => $item) {
		$link = $item->find('div[class=search-result-preview]',0);
		$image_container = $item->find('div[class=preview-container]',0);
		$image = $image_container->find('img',0);

		//description
		$description_container = $item->find('div[class=search-result-description]',0);
		$container_title = $item->find('p[class=title]',0);
		$title = $container_title->find('a',0)->title;

		$result_detail_container = $description_container->find('ul[class=result-details]',0);
		$detail = [];	
		foreach ($result_detail_container->find('li') as $key => $value) {
			if($key === 'null') continue;
			array_push($detail, $value->find('text',0)->_[4]);
		}

		//get detail content
		$detail_html = str_get_html(get_data($base_url.$link->find('a',0)->href));
		$detail_image = $detail_html->find('div[class=digital-object-reference] img',0)->src;
		$context_area_content = $detail_html->find('div[class=creatorHistories]',0)
		->find('div[class=field]',0)
		->find('div[class=history]',0)
		->plaintext;

		$context_area_year = trim($detail_html->find('div[class=creatorHistories]',0)
		->find('div[class=field]',0)
		->find('div[class=datesOfExistence]',0)
		->plaintext);

		$scope = trim($detail_html->find('section[id=contentAndStructureArea]',0)
		->find('div[class=field]',0)
		->find('div[class=scopeAndContent]',0)
		->plaintext);

		//get image name
		$array_image_name = explode('/', $detail_image);
		$image_name = '';

		if(count($array_image_name) == 9)
		{
			$image_name = $array_image_name[8];
		}

		// var_dump($array_image_name);
		// exit;

		$part = $result_detail_container->find('p a',0);
		if(count($detail) < 3) continue;

		$obj_data = [
			'link' => $base_url.$link->find('a',0)->href,
			'image_thumbnail' => $image->src,
			'title' => $title,
			'reference_code' => $detail[0],
			'level_description' => $detail[1],
			'date' => $detail[2],
			'part_of' => $part->title,
			'details' => [
				'image' => $detail_image,
				'image_name' => $image_name,
				'image_base64' => image_to_base64($base_url.$detail_image),
				'context_area' => [
					'year' => $context_area_year,
					'description' => $context_area_content
				],
				'content_area' => [
					'scope' => $scope
				]	
			]
		];

		array_push($results, $obj_data);
	}
}

// var_dump($results);

$json_data = json_encode($results);

if (file_put_contents("data.json", $json_data))
    echo "JSON file created successfully...";
else 
    echo "Oops! Error creating json file...";
