<?php
use PHPUnit\Framework\TestCase;

class connexionTest extends TestCase
{
protected $pdo;

// Set up a connection before each test
protected function setUp(): void
{
$dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=' . getenv('DB_CHARSET');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

try {
$this->pdo = new PDO($dsn, $username, $password);
$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // To get detailed errors
} catch (PDOException $e) {
$this->fail("Database connection failed: " . $e->getMessage());
}
}

// Clean up the connection after the test
protected function tearDown(): void
{
$this->pdo = null;
}

// Actual test to verify the database connection
public function testDatabaseConnection()
{
$this->assertNotNull($this->pdo); // Ensure the PDO object is not null
$this->assertInstanceOf(PDO::class, $this->pdo); // Ensure it's an instance of PDO
}
}
