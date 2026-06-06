<?php
class AzureCLoud
{
  public function BlobStorage()
  {
    $data = [
      'account_name' => getenv('AZURE_STORAGE_ACCOUNT'),
      'account_key' => getenv('AZURE_STORAGE_KEY'),
    ];

    if (empty($data['account_name']) || empty($data['account_key'])) {
      throw new Exception("Azure Blob Storage configuration is missing. Please set AZURE_STORAGE_ACCOUNT and AZURE_STORAGE_KEY environment variables.");
    }

    return $data;
  }

  public function MySQLDatabase()
  {
    $data = [
      'host' => getenv('AZURE_MYSQL_HOST'),
      'user' => getenv('AZURE_MYSQL_USERNAME'),
      'password' => getenv('AZURE_MYSQL_PASSWORD'),
      'certificate' => getenv('AZURE_MYSQL_CERTIFICATE'),
    ];

    if (empty($data['host']) || empty($data['user']) || empty($data['password']) || empty($data['certificate'])) {
      throw new Exception("Azure MySQL Database configuration is missing. Please set AZURE_MYSQL_HOST, AZURE_MYSQL_USERNAME, AZURE_MYSQL_PASSWORD, and AZURE_MYSQL_CERTIFICATE environment variables.");
    }

    return $data;
  }

  public function RedisCache()
  {
    $data = [
      'endpoint' => getenv('AZURE_REDIS_ENDPOINT'),
      'port' => getenv('AZURE_REDIS_PORT'),
      'password' => getenv('AZURE_REDIS_KEY'),
    ];

    if (empty($data['endpoint']) || empty($data['port']) || empty($data['password'])) {
      throw new Exception("Azure Redis Cache configuration is missing. Please set AZURE_REDIS_ENDPOINT, AZURE_REDIS_PORT, and AZURE_REDIS_KEY environment variables.");
    }

    return $data;
  }

  public function AppInsights()
  {
    $data = [
      'connection_string' => getenv('AZURE_APPINSIGHTS_CONNECTION_STRING'),
      'instrumentation_key' => getenv('AZURE_APPINSIGHTS_INSTRUMENTATION_KEY'),
    ];

    if (empty($data['instrumentation_key']) || empty($data['connection_string'])) {
      throw new Exception("Azure Application Insights configuration is missing. Please set either AZURE_APPINSIGHTS_INSTRUMENTATION_KEY or AZURE_APPINSIGHTS_CONNECTION_STRING environment variables.");
    }

    return $data;
  }
}
?>