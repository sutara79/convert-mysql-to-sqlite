# convert-mysql-to-sqlite
Convert a MySQL dump file to a sql file for SQLite3 using PHP.

## Demo
https://sutara79-php.herokuapp.com/demo/convert-mysql-to-sqlite/

**DO NOT** upload important files.  
If you want to do it, please download this source code and run in your local PHP environment.

## Usage in local
Please regulate the following php.ini directives.

- [memory_limit](//php.net/manual/en/ini.core.php#ini.memory-limit)
- [post_max_size](//php.net/manual/en/ini.core.php#ini.post-max-size)
- [upload_max_filesize](//php.net/manual/en/ini.core.php#ini.upload-max-filesize)

You must set the value according to this rule.
```
memory_limit > post_max_size > upload_max_filesize
```

(exapmle)
```ini
memory_limit = 128M
post_max_size = 8M
upload_max_filesize = 2M
```

## License
[MIT License](http://www.opensource.org/licenses/mit-license.php)


## Author
[Yuusaku Miyazaki](http://d.hatena.ne.jp/sutara_lumpur/20120714/1342269933)
( <toumin.m7@gmail.com> )
