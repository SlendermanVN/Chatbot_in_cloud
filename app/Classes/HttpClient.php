<?php

class HttpClient
{
  /**
   * Thực hiện HTTP Request (Tối ưu hóa cho PHP 8.5+)
   */
  public static function request($method, $url, $data = [], $headers = [], $timeout = 10)
  {
    $ch = curl_init();
    if (!$ch) {
      throw new Exception("Hệ thống không thể khởi tạo cURL.");
    }

    $method = strtoupper($method);
    $rawHeaders = is_array($headers) ? $headers : [];
    $customHeaders = [];

    foreach ($rawHeaders as $key => $value) {
      if (is_numeric($key)) {
        $customHeaders[] = $value;
      } else {
        $customHeaders[] = trim($key) . ": " . trim($value);
      }
    }

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => $timeout,
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSL_VERIFYHOST => 2,
    ];

    if ($method === "GET") {
      if (!empty($data) && is_array($data)) {
        $separator = (strpos($url, '?') === false) ? '?' : '&';
        $options[CURLOPT_URL] = $url . $separator . http_build_query($data);
      }
    } else {
      if ($method === "POST") {
        $options[CURLOPT_POST] = true;
      } else {
        $options[CURLOPT_CUSTOMREQUEST] = $method;
      }

      if (!empty($data)) {
        if (is_array($data)) {
          $options[CURLOPT_POSTFIELDS] = json_encode($data, JSON_UNESCAPED_UNICODE);

          $hasContentType = false;
          foreach ($customHeaders as $header) {
            if (stripos($header, 'Content-Type:') === 0) {
              $hasContentType = true;
              break;
            }
          }
          if (!$hasContentType) {
            $customHeaders[] = "Content-Type: application/json";
          }
        } else {
          $options[CURLOPT_POSTFIELDS] = $data;
        }
      }
    }

    $options[CURLOPT_HTTPHEADER] = $customHeaders;
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);

    if ($response === false) {
      $errorCode = curl_errno($ch);
      $errorMessage = curl_error($ch);
      // FIX PHP 8.5: Không gọi curl_close($ch) vì nó không còn tác dụng và gây Cảnh báo (Deprecated)
      throw new Exception("Lỗi kết nối đến bên thứ ba (cURL Error {$errorCode}): {$errorMessage}");
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // FIX PHP 8.5: Bỏ curl_close($ch) tại đây

    if ($httpCode < 200 || $httpCode >= 300) {
      throw new Exception("Máy chủ API trả về lỗi HTTP {$httpCode}. Nội dung phản hồi: {$response}");
    }

    return $response;
  }
}