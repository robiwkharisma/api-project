<?php

namespace App\Helpers\Util;

class QuickGit
{
    /** @var int */
    private $major = 1;

    /** @var int */
    private $minor = 0;

    /** @var string */
    private $patch = '';

    /** @var int */
    private $commits = 0;

    /** @var string */
    private $commit = '';

    /** @var string */
    private $commitDate = '';


    /**
     * @method __construct
     */
    public function __construct()
    {
        // Collect git data.
        exec('git log -1 --format="%ci"', $gitDate);
        $this->commitDate = $gitDate;

        // exec('git rev-parse --short HEAD', $gitCommit);
        // $this->commit = $gitCommit;

        // exec('git describe --always', $gitHashShort);
        // $this->patch = $gitHashShort;

        // exec('git rev-list HEAD | wc -l', $gitCommits);
        // $this->commits = $gitCommits;

        // exec('git log -1', $gitHashLong);
        // $this->commit = $gitHashLong;
    }

    /**
     * @return string
     */
    public function toString($format = 'date')
    {
        switch ($format) {
            case 'short':
                return sprintf(
                    '%d.%d.%s',
                    $this->major,
                    $this->minor,
                    $this->patch
                );
            case 'long':
                return sprintf(
                    '%d.%d.%s (#%d, %s)',
                    $this->major,
                    $this->minor,
                    $this->patch,
                    $this->commits,
                    $this->commit[0]
                );
            case 'commit':
                return $this->commit[0];
            default:
                return $this->commitDate[0];
        }
    }

    /**
     * @method __toString
     */
    public function __toString()
    {
        return $this->toString();
    }
}