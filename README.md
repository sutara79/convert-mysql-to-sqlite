# convert-mysql-to-sqlite
Convert a MySQL dump file to a sql file for SQLite3 using PHP.

## Demo
http://www.usamimi.info/~sutara/sample/convert-mysql-to-sqlite/

**DO NOT** upload a important file.  
If you want to convert a important file, please download this source code and execute in your local PHP environment.

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

## Author
Yuusaku Miyazaki (宮崎 雄策)

- Mail: toumin.m7@gmail.com
- [Blog](http://d.hatena.ne.jp/sutara_lumpur/20120714/1342269933)

## License
[MIT License](http://www.opensource.org/licenses/mit-license.php)
