<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function sprintf;
use RuntimeException;

final class WriteOperationFailedException extends RuntimeException implements Exception
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf('Cannot write to "%s"', $path));
    }
}
