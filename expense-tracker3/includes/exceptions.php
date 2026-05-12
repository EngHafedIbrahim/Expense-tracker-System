<?php
/**
 * Exception Handling Classes
 * فئات معالجة الاستثناءات
 */

/**
 * Base Exception Class
 * فئة الاستثناء الأساسية
 */
class AppException extends Exception
{
    protected $userMessage;
    protected $logMessage;
    protected $errorCode;

    public function __construct(
        $message = "حدث خطأ في التطبيق",
        $code = 0,
        $userMessage = null,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->logMessage = $message;
        $this->userMessage = $userMessage ?? $message;
        $this->errorCode = $code;
    }

    public function getUserMessage()
    {
        return $this->userMessage;
    }

    public function getLogMessage()
    {
        return $this->logMessage;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}

/**
 * Database Exception
 * استثناء قاعدة البيانات
 */
class DatabaseException extends AppException
{
    public function __construct(
        $message = "خطأ في قاعدة البيانات",
        $code = 1001,
        $userMessage = "حدث خطأ في الاتصال بقاعدة البيانات. يرجى المحاولة لاحقاً.",
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $userMessage, $previous);
    }
}

/**
 * Authentication Exception
 * استثناء المصادقة
 */
class AuthException extends AppException
{
    public function __construct(
        $message = "خطأ في المصادقة",
        $code = 2001,
        $userMessage = "خطأ في المصادقة. يرجى التحقق من بيانات الدخول.",
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $userMessage, $previous);
    }
}

/**
 * Validation Exception
 * استثناء التحقق من الصحة
 */
class ValidationException extends AppException
{
    private $errors = [];

    public function __construct(
        $message = "خطأ في التحقق من البيانات",
        $code = 3001,
        $errors = [],
        $userMessage = null,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $userMessage ?? $message, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($field, $message)
    {
        $this->errors[$field] = $message;
    }
}

/**
 * Authorization Exception
 * استثناء التفويض
 */
class AuthorizationException extends AppException
{
    public function __construct(
        $message = "غير مصرح لك بالوصول",
        $code = 4001,
        $userMessage = "ليس لديك صلاحيات كافية للوصول إلى هذا المورد.",
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $userMessage, $previous);
    }
}

/**
 * Not Found Exception
 * استثناء عدم العثور
 */
class NotFoundException extends AppException
{
    public function __construct(
        $message = "المورد غير موجود",
        $code = 5001,
        $userMessage = "المورد المطلوب غير موجود.",
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $userMessage, $previous);
    }
}

/**
 * Invalid Input Exception
 * استثناء الإدخال غير الصحيح
 */
class InvalidInputException extends AppException
{
    public function __construct(
        $message = "إدخال غير صحيح",
        $code = 6001,
        $userMessage = "البيانات المدخلة غير صحيحة. يرجى التحقق والمحاولة مجدداً.",
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $userMessage, $previous);
    }
}

/**
 * Error Logger
 * مسجل الأخطاء
 */
class ErrorLogger
{
    private static $logFile = null;
    private static $maxLogSize = 5242880; // 5MB

    public static function setLogFile($filePath)
    {
        self::$logFile = $filePath;
        // Create log directory if it doesn't exist
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public static function log($level, $message, $context = [])
    {
        if (!self::$logFile) {
            self::$logFile = dirname(__DIR__) . '/logs/error.log';
            self::setLogFile(self::$logFile);
        }

        // Rotate log file if it's too large
        if (file_exists(self::$logFile) && filesize(self::$logFile) > self::$maxLogSize) {
            self::rotateLog();
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}\n";

        error_log($logEntry, 3, self::$logFile);
    }

    public static function error($message, $context = [])
    {
        self::log('ERROR', $message, $context);
    }

    public static function warning($message, $context = [])
    {
        self::log('WARNING', $message, $context);
    }

    public static function info($message, $context = [])
    {
        self::log('INFO', $message, $context);
    }

    public static function debug($message, $context = [])
    {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            self::log('DEBUG', $message, $context);
        }
    }

    private static function rotateLog()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $rotatedFile = self::$logFile . '.' . $timestamp;
        rename(self::$logFile, $rotatedFile);

        // Keep only the last 5 rotated logs
        $logDir = dirname(self::$logFile);
        $files = glob($logDir . '/error.log.*');
        if (count($files) > 5) {
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            unlink($files[0]);
        }
    }
}

/**
 * Global Exception Handler
 * معالج الاستثناءات العام
 */
function handleException($exception)
{
    $isProduction = defined('ENVIRONMENT') && ENVIRONMENT === 'production';

    // Log the exception
    ErrorLogger::error(
        $exception->getMessage(),
        [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]
    );

    // Determine user message
    if ($exception instanceof AppException) {
        $userMessage = $exception->getUserMessage();
    } else {
        $userMessage = $isProduction 
            ? 'حدث خطأ غير متوقع. يرجى المحاولة لاحقاً.'
            : $exception->getMessage();
    }

    // Set response headers
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(500);

    // Display error page
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>خطأ - Expense Tracker</title>
        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: 'Cairo', sans-serif;
                background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(20, 184, 166, 0.10));
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 20px;
            }
            .error-container {
                background: #fff;
                border-radius: 22px;
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
                padding: 40px;
                max-width: 500px;
                text-align: center;
            }
            .error-icon {
                font-size: 4rem;
                margin-bottom: 20px;
            }
            h1 {
                margin: 0 0 16px;
                color: #ef4444;
                font-size: 1.8rem;
            }
            p {
                margin: 0 0 24px;
                color: #64748b;
                line-height: 1.6;
            }
            .error-message {
                background: rgba(239, 68, 68, 0.10);
                border: 1px solid rgba(239, 68, 68, 0.20);
                border-radius: 12px;
                padding: 16px;
                margin: 20px 0;
                color: #7f1d1d;
                font-size: 0.92rem;
                text-align: right;
            }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: linear-gradient(135deg, #2563eb, #3b82f6);
                color: #fff;
                border-radius: 12px;
                text-decoration: none;
                font-weight: 800;
                transition: 0.25s ease;
            }
            .btn:hover {
                transform: translateY(-2px);
            }
            .debug-info {
                margin-top: 30px;
                padding-top: 30px;
                border-top: 1px solid #e2e8f0;
                text-align: left;
                background: #f8fbff;
                border-radius: 12px;
                padding: 16px;
                font-family: monospace;
                font-size: 0.85rem;
                color: #64748b;
                display: <?php echo $isProduction ? 'none' : 'block'; ?>;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h1>حدث خطأ</h1>
            <p>نعتذر عن المشكلة. يرجى المحاولة لاحقاً.</p>
            
            <div class="error-message">
                <?php echo htmlspecialchars($userMessage); ?>
            </div>

            <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '/'; ?>" class="btn">
                العودة للصفحة السابقة
            </a>

            <div class="debug-info">
                <strong>معلومات التصحيح (Debug Info):</strong><br>
                <strong>الرسالة:</strong> <?php echo htmlspecialchars($exception->getMessage()); ?><br>
                <strong>الملف:</strong> <?php echo htmlspecialchars($exception->getFile()); ?><br>
                <strong>السطر:</strong> <?php echo $exception->getLine(); ?><br>
                <strong>النوع:</strong> <?php echo get_class($exception); ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/**
 * Global Error Handler
 * معالج الأخطاء العام
 */
function handleError($errno, $errstr, $errfile, $errline)
{
    ErrorLogger::error(
        "PHP Error: {$errstr}",
        [
            'errno' => $errno,
            'file' => $errfile,
            'line' => $errline
        ]
    );

    // Convert PHP errors to exceptions
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

/**
 * Fatal Error Handler
 * معالج الأخطاء الحرجة
 */
function handleFatalError()
{
    $error = error_get_last();
    if ($error !== null) {
        ErrorLogger::error(
            "Fatal Error: {$error['message']}",
            [
                'type' => $error['type'],
                'file' => $error['file'],
                'line' => $error['line']
            ]
        );
    }
}

// Register handlers
set_exception_handler('handleException');
set_error_handler('handleError');
register_shutdown_function('handleFatalError');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly
ini_set('log_errors', 1);

// Initialize error logger
ErrorLogger::setLogFile(dirname(__DIR__) . '/logs/error.log');
?>
