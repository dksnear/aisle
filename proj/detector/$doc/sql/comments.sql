
---

---- insert replace ^\s*(DROP.+|`sys_.+|PRIMARY KEY.+) ---[]
---- insert replace ^\s(`\w+`).+(,) ---[$1$2]
---- insert replace CREATE TABLE (`\w+`) ---[INSERT INTO $1]
---- insert replace ,(\r\n)\s*\).+ ---[ \) \r\n\r\nVALUES \(\);]

---- select replace ^\s*(DROP.+|`sys_.+|PRIMARY KEY.+) ---[]
---- select replace ^\s(`\w+`).+(,) ---[$1$2]
---- select replace ,(\r\n)\s*\).+ ---[\);]
---- select replace CREATE TABLE (`\w+`) \(([^\)]+)\) ---[SELECT $2 FROM $1 LIMIT 5] --new line