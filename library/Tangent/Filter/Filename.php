<?php

/**
 *   Windows filename conventions:
 *   http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247(v=vs.85).aspx#naming_conventions
 *
 *   Mac OS 9/X conventions:
 *   http://support.grouplogic.com/?p=1607
 *
 *   Linux filename conventions:
 *   http://www.linfo.org/file_name.html
 *
 *   The following reserved characters cannot be in a Windows filename:
        < (less than)
        > (greater than)
        : (colon)         -- Cannot be used in Mac OS 9 or OS X
        " (double quote)
        / (forward slash)
        \ (backslash)     -- Cannot be used in Unix/Linux systems
        | (vertical bar or pipe)
        ? (question mark)
         * (asterisk)
 *
 *  Also, do not end a file or directory name with a space or a period. Although the underlying file
 *  system may support such names, the Windows shell and user interface does not. However, it is
 *  acceptable to specify a period as the first character of a name. For example, ".temp"
 */

class Tangent_Filter_Filename implements Zend_Filter_Interface
{
    /**
     *  Convert a given string into an acceptable filename.  See above for details on valid filenames.
     *
     * @param  array|string $filename
     *
     * @throws Zend_Filter_Exception If filtering $value is impossible
     * @return array|string
     */
    public function filter ($filename)
    {
        if (is_array($filename))
        {
            foreach ($filename as $key => $value)
            {
                $filename[$key] = $this->filter($value);
            }
        }
        else
        {
            // Trim any leading/trailing spaces.
            $filename = trim($filename, ' ');

            // Transliterate to ASCII.
            $transliterator = Transliterator::create("Any-Latin; Latin-ASCII");
            $filename       = transliterator_transliterate($transliterator, $filename);

            // Replace whitespace
            $filename = preg_replace('/\s+/', '_', $filename);

            // Replace separators
            $filename = str_replace('-', '_', $filename);
            $filename = str_replace('/', '_', $filename);
            $filename = str_replace('&', '_', $filename);
            $filename = str_replace('\\', '_', $filename);

            // Remove any remaining non-safe characters.
            $filename = preg_replace('/[^0-9A-Za-z_.]/', '', $filename);

            // Replace multiple consecutive underscores with a single underscore
            $filename = preg_replace('/_+/', '_', $filename);

            // Replace multiple consecutive dots with a single dot
            $filename = preg_replace('/\.+/', '.', $filename);

            // Trim any leading underscores
            $filename = ltrim($filename, '_');

            // Trim any trailing dots or underscores
            $filename = rtrim($filename, '._');

        }
        return $filename;
    }
}

