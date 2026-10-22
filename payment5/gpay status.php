<?php

$AT = 'AFMS8ht6u7RCT_D7z8usvEOGrz0j:1759653246125';
$FREQ_RAW = '[[["RPtkab","[\"BCR2DN4T5HYOZ4AP\",null,[null,10],1,null,1]",null,"4"]]]';

$FSID = '-1165716089292329246';
$REQID = '962571';
$BL = 'boq_payments-merchant-console-ui_20250803.08_p0';
$HL = 'en-GB';
$SOURCE_PATH = '/g4b/transactions/BCR2DN4T5HYOZ4AP';
$ORIGIN = 'https://pay.google.com';

$COOKIE = 'SID=g.a0001whqOA_B1rObgdZt4cq0Rnwq1hAaIw5FMu--9tQhBwxU7CaqTYvsw02AEqflgZrkVGnSGgACgYKASASARYSFQHGX2MiNlbP-0IQYRZIcYwRNr9_ERoVAUF8yKqi5F3jluLFu4H1MBKK0KaA0076; __Secure-1PSID=g.a0001whqOA_B1rObgdZt4cq0Rnwq1hAaIw5FMu--9tQhBwxU7CaqWi3ceb9GtVlH5OUmH7SV6wACgYKAW0SARYSFQHGX2Mi14w3miZkwSBa4tJy9jdeqBoVAUF8yKqZ_uyp52_pMmj_QhCMm90G0076; __Secure-3PSID=g.a0001whqOA_B1rObgdZt4cq0Rnwq1hAaIw5FMu--9tQhBwxU7CaqOoxVPzNhl-P5haM8TGM8zwACgYKAW8SARYSFQHGX2Mi9JVoVSp-Z9gLKEMuoDIu0BoVAUF8yKrBhEjvATcqhnEJnjLFMHFU0076; HSID=AiI5ITgU63U2x64Mg; SSID=AlCxiAGaifM31g0AX; APISID=XV6bgINLoXe8WOy7/AvJIRX132umtiJFkx; SAPISID=3VgtRz0F_DZtx3pr/AWlYMy6E-j0QWWKuA; __Secure-1PAPISID=3VgtRz0F_DZtx3pr/AWlYMy6E-j0QWWKuA; __Secure-3PAPISID=3VgtRz0F_DZtx3pr/AWlYMy6E-j0QWWKuA; SEARCH_SAMESITE=CgQIjJ8B; AEC=AaJma5vjAHIKMmuMY9samw8Gsxccuoj-tmA7PHdC9DazfbakCwX4vwVpG68; NID=525=Pd1tsnx7XhjZmaIPrbijBweRgTkJoWTfT9f04cYvlWoXc_nH6WnBVp4W9B2HLqo-EzGMg42YLdbSwd2X9ZBmp29BuQlVcZS5gC7VH1d9Cg3G7PIVapiXU47q-6F_NCnov63May3OdEoMbp74rws4rMsORQPSZRbtsqRwY_r_nuUy8yJwCLCHAxP9TYW0pucelwvqMDPX30pj_MNgBrRoHeN4s7_pFqWTqeV33boeYVIfZ-k7qE2dbmY0z7sQSkV_MOFmUlWT5HtxtttfqtCukRgPNf1FwE5iduduOTZKTFyIieiUCQ6Fvy4i885yBPMr7jIDaQypZX5qBcfWV6hx9oe2wpM3J6fvVeQB-277vPQWuA; _ga_5WYRGW7L7J=GS2.1.s1759653249$o1$g0$t1759653249$j60$l0$h0; _ga=GA1.1.1235734649.1759653249; OTZ=8289154_34_34__34_; __Secure-1PSIDTS=sidts-CjIBmkD5S4dT4IZRHmVgSi6J5px9IX09CwmwOWH6v9ZM0P9vvm3yck4VUibUoYUZJIGkmhAA; __Secure-3PSIDTS=sidts-CjIBmkD5S4dT4IZRHmVgSi6J5px9IX09CwmwOWH6v9ZM0P9vvm3yck4VUibUoYUZJIGkmhAA; SIDCC=AKEyXzUAS5lSxhHw4xDqqYJyQCUK1CsBrWerbqewzxPnCYQhnF7I58ys1IVoKCl0zPoFwPWfiw; __Secure-1PSIDCC=AKEyXzUubQACXQIEdsbAih5wocDE-78X6l5s3YBhPQftI8LNCXaiX22zZ1oFJeZHYmJ0Ec8I3Fg; __Secure-3PSIDCC=AKEyXzXNJaTHXDvkKv-Z7j4Nnj8nLeWLPp9yPQyZMNe6dx9boidIMBVV5gJAPvspngIMIhL74g'; 

// yaha per order id paas karna hai 
$FILTER_REMARKS = "IMBPG1757598829";

$base = 'https://pay.google.com/g4b/_/SMBConsoleUI/data/batchexecute';
$q = http_build_query([
  'rpcids'       => 'RPtkab',
  'source-path'  => $SOURCE_PATH,
  'f.sid'        => $FSID,
  'bl'           => $BL,
  'hl'           => $HL,
  'soc-app'      => '1',
  'soc-platform' => '1',
  'soc-device'   => '2',
  '_reqid'       => $REQID,
  'rt'           => 'c',
], '', '&', PHP_QUERY_RFC3986);
$url = $base.'?'.$q;

function cookie_value(string $blob, string $name): ?string {
  foreach (explode(';', $blob) as $p) { 
    $kv = explode('=', trim($p), 2); 
    if (count($kv)==2 && $kv[0]===$name) return $kv[1]; 
  }
  return null;
}
function sapisidhash(string $cookie, string $origin): ?string {
  $sapisid = cookie_value($cookie, 'SAPISID') ?: cookie_value($cookie, '__Secure-3PAPISID');
  if (!$sapisid) return null;
  $ts = (string) time();
  return 'SAPISIDHASH '.$ts.'_'.sha1($ts.' '.$sapisid.' '.$origin);
}

$headers = [
  'accept: */*',
  'content-type: application/x-www-form-urlencoded;charset=UTF-8',
  'origin: '.$ORIGIN,
  'referer: '.$ORIGIN.'/',
  'accept-encoding: gzip, deflate, br, zstd',
  'accept-language: en-US,en;q=0.9,hi;q=0.8',
  'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36',
  'sec-fetch-dest: empty',
  'sec-fetch-mode: cors',
  'sec-fetch-site: same-origin',
  'x-browser-channel: stable',
  'x-browser-year: 2025',
  'x-same-domain: 1',
  'x-client-data: CIu2yQEIprbJAQipncoBCLbgygEIk6HLAQiko8sBCIWgzQEI/qXOAQjrgM8BCPaDzwEIgYTPAQiVhM8BCKCFzwEY4eLOARjS/s4B',
  "cookie: $COOKIE",
];
if ($auth = sapisidhash($COOKIE, $ORIGIN)) {
  $headers[] = 'authorization: '.$auth;
  $headers[] = 'x-origin: '.$ORIGIN;
}

if ($AT === 'PASTE_FRESH_AT_HERE') {
  die('❌ DevTools से fresh `at` token $AT में पेस्ट करें.');
}

$useRaw = (strncmp($FREQ_RAW, '%5B', 3) === 0 || strncmp($FREQ_RAW, '%5b', 3) === 0);
if ($useRaw) {
  $postBody = 'f.req='.$FREQ_RAW.'&at='.rawurlencode($AT);
} else {
  $postBody = http_build_query(['f.req' => $FREQ_RAW, 'at' => $AT], '', '&', PHP_QUERY_RFC3986);
}

$ch = curl_init();
curl_setopt_array($ch, [
  CURLOPT_URL            => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => $headers,
  CURLOPT_POSTFIELDS     => $postBody,
  CURLOPT_ENCODING       => '',
  CURLOPT_TIMEOUT        => 30,
]);
$raw  = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($err) { header('Content-Type:text/plain'); die("cURL error: $err"); }

function parse_chunks(string $resp): array {
  if (strpos($resp, ")]}'") === 0) $resp = substr($resp, 4);
  $resp = ltrim($resp, "\r\n");
  $lines = preg_split("/\r\n|\n|\r/", $resp);
  $out = [];
  for ($i=0; $i<count($lines); ) {
    while ($i<count($lines) && trim($lines[$i])==='') $i++;
    if ($i>=count($lines)) break;
    $len = trim($lines[$i++]);
    while ($i<count($lines) && trim($lines[$i])==='') $i++;
    if ($i>=count($lines)) break;
    $json = $lines[$i++];

    $arr = json_decode($json, true);
    $rpc = $arr[0][1] ?? null;
    $payload = $arr[0][2] ?? null;

    for ($k=0; $k<3; $k++) {
      if (is_string($payload)) { 
        $tmp = json_decode($payload, true); 
        if ($tmp !== null) $payload = $tmp; else break; 
      }
    }
    $out[] = ['rpc'=>$rpc, 'payload'=>$payload, 'raw'=>$json, 'len'=>ctype_digit($len)?(int)$len:null];
  }
  return $out;
}

if ($code !== 200) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'status' => 'error',
    'http_code' => $code,
    'message' => substr((string)$raw,0,700),
    'note' => "Note: 400 अक्सर wrong f.req encoding या expired `at` से आता है. अभी useRaw=" . ($useRaw?'true':'false')
  ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
  exit;
}

$chunks = parse_chunks((string)$raw);
$payload = null;
foreach ($chunks as $c) if (($c['rpc'] ?? null) === 'RPtkab') { $payload = $c['payload']; break; }

if ($payload === null) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'status' => 'error',
    'message' => "RPtkab payload नहीं मिला",
    'raw_preview' => substr((string)$raw,0,400)
  ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
  exit;
}

function map_txns(array $payload): array {
  if (!isset($payload[0]) || !is_array($payload[0])) return [];
  $txns = $payload[0]; $out = [];
  foreach ($txns as $t) {
    if (!is_array($t)) continue;
    $sec = $t[2][0] ?? null;
    $out[] = [
      'txn_id'     => $t[0] ?? '',
      'order_id'   => $t[1] ?? '',
      'time'       => $sec ? date('Y-m-d H:i:s', (int)$sec) : '',
      'amount'     => $t[3][1] ?? null,
      'currency'   => $t[3][0] ?? '',
      'payer_name' => $t[8][0] ?? '',
      'payer_upi'  => $t[8][1] ?? '',
      'remarks'    => $t[9] ?? '',
      'status'     => (isset($t[10]) && (int)$t[10] === 5) ? 'SUCCESS' : 'PENDING/FAILED',
    ];
  }
  return $out;
}

$txnList = map_txns($payload);

// 🔎 Filter सिर्फ वही txn दिखेगा जिसका remarks match करे
if ($FILTER_REMARKS) {
  $txnList = array_values(array_filter($txnList, function($t) use ($FILTER_REMARKS) {
    return isset($t['remarks']) && $t['remarks'] === $FILTER_REMARKS;
  }));
}

// ✅ JSON output
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'status' => 'success',
  'count'  => count($txnList),
  'transactions' => $txnList
], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
exit;

