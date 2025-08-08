<?php
$token = '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';
$repo = 'supun123456789/soft';
$branch = 'main';
$filePath = 'vrf_models.xlsx';
$uploadPath = 'vrf_models.xlsx';

$content = base64_encode(file_get_contents($filePath));
$url = "https://api.github.com/repos/$repo/contents/$uploadPath";

$options = [
  'http' => [
    'method' => 'PUT',
    'header' => "Authorization: token $token\r\n" .
                "User-Agent: VRF-Uploader\r\n" .
                "Content-Type: application/json\r\n",
    'content' => json_encode([
      'message' => 'Upload VRF Excel',
      'content' => $content,
      'branch' => $branch
    ])
  ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

header('Content-Type: application/json');
echo $result;
?>