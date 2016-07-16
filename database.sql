--
-- Baza danych: `FlyTalk`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `uid`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(64)      NOT NULL,
  `val`   VARCHAR(64)      NOT NULL,
  PRIMARY KEY (`uid`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 3;

--
-- Zrzut danych tabeli `config`
--

INSERT INTO `config` (`uid`, `title`, `val`) VALUES
  (1, 'sitename', 'FlyTalk'),
  (2, 'theme', '1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `set`
--

CREATE TABLE IF NOT EXISTS `set` (
  `uid`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `theme` INT(10) UNSIGNED NOT NULL,
  `group` VARCHAR(64)      NOT NULL,
  `name`  VARCHAR(64)               DEFAULT NULL,
  `value` TEXT             NOT NULL,
  PRIMARY KEY (`uid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 2;

--
-- Zrzut danych tabeli `set`
--

INSERT INTO `set` (`uid`, `theme`, `group`, `name`, `value`) VALUES
  (1, 1, 'global', 'global',
   '<!DOCTYPE html>\r\n<html lang="en">\r\n    <head>\r\n<title>{$title}</title>\r\n        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">\r\n        <meta charset="utf-8">\r\n        <meta http-equiv="X-UA-Compatible" content="IE=edge">\r\n        <meta name="viewport" content="width=device-width, initial-scale=1.0">\r\n        <meta name="description" content="">\r\n        <meta name="author" content="">\r\n    </head>\r\n    <body>\r\n        <header>\r\n            <div class="main_width">\r\n                \r\n            </div>\r\n        </header>\r\n        <main>\r\n            <div class="main_width">\r\n                {$body}\r\n            </div>\r\n        </main>\r\n        <footer>\r\n            <div class="main_width">\r\n                \r\n            </div>\r\n        </footer>\r\n    </body>\r\n</html>');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `uid`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255)              DEFAULT NULL,
  PRIMARY KEY (`uid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 2;

--
-- Zrzut danych tabeli `themes`
--

INSERT INTO `themes` (`uid`, `name`) VALUES
  (1, 'Default Theme');