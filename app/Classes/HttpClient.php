<?php

class HttpClient
{
  /**
   * Thực hiện HTTP Request (Hỗ trợ JSON API và Binary Upload cho Cloud Storage)
   * * @param string $method HTTP Method (GET, POST, PUT, DELETE)
   * @param string $url Endpoint URL
   * @param mixed $data Mảng dữ liệu cho JSON/Query hoặc Chuỗi Nhị phân thô cho File Upload
   * @param array $headers Mảng các HTTP Header truyền vào (Mảng phẳng: ["Header-Name: Value"])
   * @param int $timeout Tổng thời gian tối đa chờ phản hồi (giây)
   * @return string Response thô từ server
   * @throws Exception
   */
  public static function request($method, $url, $data = [], $headers = [], $timeout = 10)
  {
    $ch = curl_init();
    if (!$ch) {
      throw new Exception("Hệ thống không thể khởi tạo cURL.");
    }

    $method = strtoupper($method);

    // 1. KIẾN TRÚC ĐẦU VÀO: Đảm bảo mảng Header luôn hợp lệ để trộn cấu hình
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
      CURLOPT_CONNECTTIMEOUT => 5, // Tránh treo tiến trình PHP quá 5s nếu mạng sập
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSL_VERIFYHOST => 2,
    ];

    // 2. XỬ LÝ ĐA PHƯƠNG THỨC (GET, POST, PUT, DELETE) VÀ KIỂU DỮ LIỆU
    if ($method === "GET") {
      if (!empty($data) && is_array($data)) {
        // Kiểm tra xem URL đã có sẵn dấu "?" chưa để nối query string cho đúng
        $separator = (strpos($url, '?') === false) ? '?' : '&';
        $options[CURLOPT_URL] = $url . $separator . http_build_query($data);
      }
    } else {
      // Cấu hình Method cho POST, PUT, DELETE
      if ($method === "POST") {
        $options[CURLOPT_POST] = true;
      } else {
        $options[CURLOPT_CUSTOMREQUEST] = $method;
      }

      // Xử lý Body dữ liệu (Phân biệt giữa Payload JSON và Binary File Upload)
      if (!empty($data)) {
        if (is_array($data)) {
          // Nếu là mảng -> Tự động chuyển thành JSON và set Content-Type
          $options[CURLOPT_POSTFIELDS] = json_encode($data);

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
          // SỬA LỖI CHO AZURE: Nếu là chuỗi/nhị phân -> Đẩy thẳng làm thô (Dùng cho upload ảnh)
          $options[CURLOPT_POSTFIELDS] = $data;
        }
      }
    }

    // SỬA LỖI NGHIÊM TRỌNG: Gán mảng header vào đúng thuộc tính CURLOPT_HTTPHEADER
    $options[CURLOPT_HTTPHEADER] = $customHeaders;

    curl_setopt_array($ch, $options);

    // 3. THỰC THI VÀ ERROR HANDLING
    $response = curl_exec($ch);

    if ($response === false) {
      $errorCode = curl_errno($ch);
      $errorMessage = curl_error($ch);
      curl_close($ch);
      throw new Exception("Lỗi kết nối đến bên thứ ba (cURL Error {$errorCode}): {$errorMessage}");
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Chuẩn REST: Các mã từ 400 trở lên đều là lỗi (Client Error hoặc Server Error)
    if ($httpCode < 200 || $httpCode >= 300) {
      throw new Exception("Máy chủ API trả về lỗi HTTP {$httpCode}. Nội dung phản hồi: {$response}");
    }

    return $response;
  }
}