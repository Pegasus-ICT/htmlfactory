<?php /** @noinspection PhpIncludeInspection */
declare(strict_types=1);

namespace {
    const EXT = ".class.php";
}

namespace PegasusICT\HtmlFactory {
    class AutoLoader {
        public static function loader( string $className ): void {
            $file = null;
            $className = array_pop(explode('\\',$className));
            foreach(['controller', 'interface', 'model'] as $item)
                if(strpos($className, ucfirst($item)) !== false)
                    $file = __CLASS__ . "/../$item/" . $className . EXT;
            $file= (!empty($file)) ? $file : __CLASS__ . "/../system/" . $className . EXT;
            if(file_exists($file)) require_once $file;
        }
    }
}

namespace PegasusICT\PhpHelpers {
    class AutoLoader {
        public static function loader( string $className ): void {
            $file = null;
            $className = array_pop(explode('\\',$className));
            foreach(['exception','logger'] as $item)
                if(strpos($className, ucfirst($item)) !== false)
                    $file = __CLASS__ . "/../$item/" . $className . EXT;
            $file= (!empty($file)) ? $file : __CLASS__ . "/../system/" . $className . EXT;
            if(file_exists($file)) require_once $file;
        }
    }
}

namespace {
    spl_autoload_register('\PegasusICT\HtmlFactory\AutoLoader::loader');
    spl_autoload_register('\PegasusICT\PhpHelpers\AutoLoader::loader');
}
