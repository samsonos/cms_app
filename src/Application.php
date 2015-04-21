<?php 
namespace samsoncms;

use samson\core\CompressableExternalModule;

/**
 * SamsonCMS external compressible application for integrating
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Application extends CompressableExternalModule
{
    /** Application name */
    public $name;

    /** Flag for hiding Application icon in main menu */
    public $hide = false;

    /** @var string Application main menu icon */
    public $icon = 'book';
}
