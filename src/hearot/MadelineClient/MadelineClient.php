<?php
namespace hearot\MadelineClient;

class Client
{
    const MADELINECLIENT_DEFAULT_SETTINGS = '{"app_info":{"api_id":6,"api_hash":"eb06d4abfb49dc3eeb1aeb98ae0f581e"}}';
    const MADELINECLIENT_VERSION = '1.0';
    public function __construct($settings = '{"app_info":{"api_id":6,"api_hash":"eb06d4abfb49dc3eeb1aeb98ae0f581e"}}')
    {
        global $argv;
        $this->settings = (is_array($settings)) ? $settings : null;
        if (in_array($argv[1], ['-v', '-version', '-ver'])) {
            echo 'MadelineProto CLI Version ' . MADELINECLIENT_VERSION . "\nDeveloped by Hearot, MadelineProto by Daniil Gentili.\nCopyright 2017" . PHP_EOL;
            exit;
        } elseif (in_array($argv[1], ['-load', '-l'])) {
            $this->load($argv[2]);
            exit;
        } elseif (in_array($argv[1], ['-n', '-new', '-novo'])) {
            $this->new();
            exit;
        } else {
            $this->new_session();
            exit;
        }
    }
    private function load($path)
    {
        if (file_exists($path)) {
            try {
                $MadelineProto = \danog\MadelineProto\Serialization::deserialize($path);
            } catch (\danog\MadelineProto\Exception $e) {
                echo 'Not valid .madeline file, please re-create it.' . PHP_EOL;
                exit;
            }
            while (1) {
                try {
                    $r = readline('> ');
                    if (empty($r)) {
                        continue;
                    }
                    switch (strtolower($r)) {
              case 'version':
              case '-v':
                  echo 'MadelineProto CLI Version ' . $this->MADELINECLIENT_VERSION . "\nDeveloped by Hearot, MadelineProto by Daniil Gentili.\nCopyright 2017" . PHP_EOL;
        continue 2;
              break;
              case 'exit':
                  echo 'Bye!' . PHP_EOL;
                  exit;
              break;
          }
                    $l = explode(' ', $r);
                    try {
                        $method = $MadelineProto->API->methods->find_by_method($l[0]);
                    } catch (Exception $e) {
                        continue;
                    }
                    if ($method and count($l) == 1) {
                        $help_string = '';
                        foreach ($method['params'] as $method_param) {
                            $help_string .= $method_param['name'] . ' ';
                        }
                        echo 'Help: ' . $l[0] . ' ' . $help_string . PHP_EOL;
                        continue;
                    } elseif ($method) {
                        $node = explode('.', $l[0]);
                        $param_array = array();
                        $i = 1;
                        foreach ($method['params'] as $method_param) {
                            $param_array[$method_param['name']] = $l[$i++];
                        }
                        var_dump($MadelineProto->{$node[0]}->{$node[1]}($param_array));
                    }
                    \danog\MadelineProto\Serialization::serialize($path, $MadelineProto);
                } catch (Exception $e) {
                    continue;
                }
            }
            exit;
        } else {
            echo 'The file doesn\'t exist. Please submit a valid .madeline file.' . PHP_EOL;
            exit;
        }
    }
    private function new()
    {
        echo 'This script will create a MadelineProto project and a composer.json file for it. Please answer to the following questions.' . PHP_EOL . PHP_EOL;
        $nick = strtolower(latinreadline('Your nickname (example: danog): '));
        echo PHP_EOL;
        $name = readline('Your name (example: Daniil Gentili): ');
        echo PHP_EOL;
        $email = emailreadline('Your email (example: daniil@daniil.it): ');
        echo PHP_EOL;
        $project = platinreadline('Project name (example: myMadelineProtobot): ');
        echo PHP_EOL;
        $description = readline('Project description (example: An awesome MadelineProto bot!): ');
        echo PHP_EOL;
        echo PHP_EOL;
        $project_composer = '';

        foreach (str_split($project) as $c) {
            if (ctype_upper($c)) {
                $project_composer .= '-' . strtolower($c);
            } else {
                $project_composer .= $c;
            }
        }

        $res = ['name' => $nick . '/' . $project_composer, 'description' => $description, 'license' => 'AGPLv3', 'authors' => [['name' => $name, 'email' => $email]], 'type' => $project, 'require' => ['danog/madelineproto' => 'dev-master'],

    'autoload' => ['psr-0' => [$nick . '\\' . $project . '\\' => 'src/']], 'repositories' => [['type' => 'git', 'url' => 'https://github.com/danog/phpseclib']], 'minimum-stability' => 'dev'];

        mkdir($project);
        mkdir($project . '/src');
        mkdir($project . '/src/' . $nick);
        mkdir($project . '/src/' . $nick . '/' . $project);
        file_put_contents($project . '/composer.json', json_encode($res, JSON_PRETTY_PRINT));
        $a = 'IyEvdXNyL2Jpbi9lbnYgcGhwCjw/cGhwCi8qCkNvcHlyaWdodCAyMDE2LTIwMTcgRGFuaWlsIEdlbnRpbGkKKGh0dHBzOi8vZGFuaWlsLml0KQpUaGlzIGZpbGUgaXMgcGFydCBvZiBNYWRlbGluZVByb3RvLgpNYWRlbGluZVByb3RvIGlzIGZyZWUgc29mdHdhcmU6IHlvdSBjYW4gcmVkaXN0cmlidXRlIGl0IGFuZC9vciBtb2RpZnkgaXQgdW5kZXIgdGhlIHRlcm1zIG9mIHRoZSBHTlUgQWZmZXJvIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgYXMgcHVibGlzaGVkIGJ5IHRoZSBGcmVlIFNvZnR3YXJlIEZvdW5kYXRpb24sIGVpdGhlciB2ZXJzaW9uIDMgb2YgdGhlIExpY2Vuc2UsIG9yIChhdCB5b3VyIG9wdGlvbikgYW55IGxhdGVyIHZlcnNpb24uCk1hZGVsaW5lUHJvdG8gaXMgZGlzdHJpYnV0ZWQgaW4gdGhlIGhvcGUgdGhhdCBpdCB3aWxsIGJlIHVzZWZ1bCwgYnV0IFdJVEhPVVQgQU5ZIFdBUlJBTlRZOyB3aXRob3V0IGV2ZW4gdGhlIGltcGxpZWQgd2FycmFudHkgb2YgTUVSQ0hBTlRBQklMSVRZIG9yIEZJVE5FU1MgRk9SIEEgUEFSVElDVUxBUiBQVVJQT1NFLgpTZWUgdGhlIEdOVSBBZmZlcm8gR2VuZXJhbCBQdWJsaWMgTGljZW5zZSBmb3IgbW9yZSBkZXRhaWxzLgpZb3Ugc2hvdWxkIGhhdmUgcmVjZWl2ZWQgYSBjb3B5IG9mIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSBhbG9uZyB3aXRoIE1hZGVsaW5lUHJvdG8uCklmIG5vdCwgc2VlIDxodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvPi4KKi8Kc2hlbGxfZXhlYygnY29tcG9zZXIgdXBkYXRlJyk7CgpyZXF1aXJlICd2ZW5kb3IvYXV0b2xvYWQucGhwJzsKJHNldHRpbmdzID0gWydhcHBfaW5mbycgPT4gWydhcGlfaWQnID0+IDYsICdhcGlfaGFzaCcgPT4gJ2ViMDZkNGFiZmI0OWRjM2VlYjFhZWI5OGFlMGY1ODFlJ11dOwoKdHJ5IHsKICAgICRib3QgPSBcZGFub2dcTWFkZWxpbmVQcm90b1xTZXJpYWxpemF0aW9uOjpkZXNlcmlhbGl6ZSgnYm90Lm1hZGVsaW5lJyk7Cn0gY2F0Y2ggKFxkYW5vZ1xNYWRlbGluZVByb3RvXEV4Y2VwdGlvbiAkZSkgewogICAgdmFyX2R1bXAoJGUtPmdldE1lc3NhZ2UoKSk7CiAgICB3aGlsZSAodFJ1ZSkgewogICAgICAgICR1c2VyID0gcmVhZGxpbmUoJ0RvIHlvdSB3YW50IHRvIGxvZ2luIGFzIGEgdXNlciBvciBhcyBhIGJvdCAodXNlci9ib3QpPyAnKTsKICAgICAgICBzd2l0Y2ggKCR1c2VyKSB7CiAgICAgICAgICAgIGNhc2UgJ3VzZXInOgogICAgICAgICAgICAgICAgJE1hZGVsaW5lUHJvdG8gPSBuZXcgXGRhbm9nXE1hZGVsaW5lUHJvdG9cQVBJKCRzZXR0aW5ncyk7CiAgICAgICAgICAgICAgICAkc2VudENvZGUgPSAkTWFkZWxpbmVQcm90by0+cGhvbmVfbG9naW4ocmVhZGxpbmUoJ0VudGVyIHRoZSBwaG9uZSBudW1iZXI6ICcpKTsKICAgICAgICAgICAgICAgICRjb2RlID0gcmVhZGxpbmUoJ0VudGVyIHRoZSBjb2RlIHlvdSByZWNlaXZlZDogJyk7CiAgICAgICAgICAgICAgICAkYXV0aG9yaXphdGlvbiA9ICRNYWRlbGluZVByb3RvLT5jb21wbGV0ZV9waG9uZV9sb2dpbigkY29kZSk7CiAgICAgICAgICAgICAgICBpZiAoJGF1dGhvcml6YXRpb25bJ18nXSA9PT0gJ2FjY291bnQubm9QYXNzd29yZCcpIHsKICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBcZGFub2dcTWFkZWxpbmVQcm90b1xFeGNlcHRpb24oJzJGQSBpcyBlbmFibGVkIGJ1dCBubyBwYXNzd29yZCBpcyBzZXQhJyk7CiAgICAgICAgICAgICAgICB9CiAgICAgICAgICAgICAgICBpZiAoJGF1dGhvcml6YXRpb25bJ18nXSA9PT0gJ2FjY291bnQucGFzc3dvcmQnKSB7CiAgICAgICAgICAgICAgICAgICBcZGFub2dcTWFkZWxpbmVQcm90b1xMb2dnZXI6OmxvZyhbJzJGQSBpcyBlbmFibGVkJ10sIFxkYW5vZ1xNYWRlbGluZVByb3RvXExvZ2dlcjo6Tk9USUNFKTsKICAgICAgICAgICAgICAgICAgICRhdXRob3JpemF0aW9uID0gJE1hZGVsaW5lUHJvdG8tPmNvbXBsZXRlXzJmYV9sb2dpbihyZWFkbGluZSgnUGxlYXNlIGVudGVyIHlvdXIgcGFzc3dvcmQgKGhpbnQgJy4kYXV0aG9yaXphdGlvblsnaGludCddLicpOiAnKSk7CiAgICAgICAgICAgICAgICB9CiAgICAgICAgICAgICAgICBpZiAoJGF1dGhvcml6YXRpb25bJ18nXSA9PT0gJ2FjY291bnQubmVlZFNpZ251cCcpIHsKICAgICAgICAgICAgICAgICAgIFxkYW5vZ1xNYWRlbGluZVByb3RvXExvZ2dlcjo6bG9nKFsnUmVnaXN0ZXJpbmcgbmV3IHVzZXInXSwgXGRhbm9nXE1hZGVsaW5lUHJvdG9cTG9nZ2VyOjpOT1RJQ0UpOwogICAgICAgICAgICAgICAgICAgJGF1dGhvcml6YXRpb24gPSAkTWFkZWxpbmVQcm90by0+Y29tcGxldGVfc2lnbnVwKHJlYWRsaW5lKCdQbGVhc2UgZW50ZXIgeW91ciBmaXJzdCBuYW1lOiAnKSwgcmVhZGxpbmUoJ1BsZWFzZSBlbnRlciB5b3VyIGxhc3QgbmFtZSAoY2FuIGJlIGVtcHR5KTogJykpOwogICAgICAgICAgICAgICAgfQogICAgICAgICAgICAgICAgXGRhbm9nXE1hZGVsaW5lUHJvdG9cTG9nZ2VyOjpsb2coWyRhdXRob3JpemF0aW9uXSwgXGRhbm9nXE1hZGVsaW5lUHJvdG9cTG9nZ2VyOjpOT1RJQ0UpOwoKICAgICAgICAgICAgICAgICRib3QgPSBuZXcgXHN1Ym1lYVxzdWJtZWJcTWFpbigkTWFkZWxpbmVQcm90byk7CgogICAgICAgICAgICAgICAgYnJlYWsgMjsKICAgICAgICAgICAgY2FzZSAnYm90JzoKICAgICAgICAgICAgICAgICRNYWRlbGluZVByb3RvID0gbmV3IFxkYW5vZ1xNYWRlbGluZVByb3RvXEFQSSgkc2V0dGluZ3MpOwogICAgICAgICAgICAgICAgJGF1dGhvcml6YXRpb24gPSAkTWFkZWxpbmVQcm90by0+Ym90X2xvZ2luKHJlYWRsaW5lKCdFbnRlciB0aGUgYm90IHRva2VuOiAnKSk7CiAgICAgICAgICAgICAgICBcZGFub2dcTWFkZWxpbmVQcm90b1xMb2dnZXI6OmxvZyhbJGF1dGhvcml6YXRpb25dLCBcZGFub2dcTWFkZWxpbmVQcm90b1xMb2dnZXI6Ok5PVElDRSk7CgogICAgICAgICAgICAgICAgJGJvdCA9IG5ldyBcc3VibWVhXHN1Ym1lYlxNYWluKCRNYWRlbGluZVByb3RvKTsKCiAgICAgICAgICAgICAgICBicmVhayAyOwogICAgICAgICAgICBkZWZhdWx0OgogICAgICAgICAgICAgICAgZWNobyAnUGxlYXNlIHdyaXRlICJ1c2VyIiBvciAiYm90IicuUEhQX0VPTDsKICAgICAgICB9CiAgICB9Cn0KCmVjaG8gJ1dyb3RlICcuXGRhbm9nXE1hZGVsaW5lUHJvdG9cU2VyaWFsaXphdGlvbjo6c2VyaWFsaXplKCdib3QubWFkZWxpbmUnLCAkYm90KS4nIGJ5dGVzJy5QSFBfRU9MOwoKCiRvZmZzZXQgPSAwOwp3aGlsZSAodHJ1ZSkgewogICAgJHVwZGF0ZXMgPSAkYm90LT5NYWRlbGluZVByb3RvLT5BUEktPmdldF91cGRhdGVzKFsnb2Zmc2V0JyA9PiAkb2Zmc2V0LCAnbGltaXQnID0+IDUwLCAndGltZW91dCcgPT4gMF0pOyAvLyBKdXN0IGxpa2UgaW4gdGhlIGJvdCBBUEksIHlvdSBjYW4gc3BlY2lmeSBhbiBvZmZzZXQsIGEgbGltaXQgYW5kIGEgdGltZW91dAogICAgXGRhbm9nXE1hZGVsaW5lUHJvdG9cTG9nZ2VyOjpsb2coWyR1cGRhdGVzXSk7CiAgICBmb3JlYWNoICgkdXBkYXRlcyBhcyAkdXBkYXRlKSB7CiAgICAgICAgJG9mZnNldCA9ICR1cGRhdGVbJ3VwZGF0ZV9pZCddICsgMTsgLy8gSnVzdCBsaWtlIGluIHRoZSBib3QgQVBJLCB0aGUgb2Zmc2V0IG11c3QgYmUgc2V0IHRvIHRoZSBsYXN0IHVwZGF0ZV9pZAogICAgICAgICRib3QtPnsnb24nLnVjZmlyc3QoJHVwZGF0ZVsndXBkYXRlJ11bJ18nXSl9KCR1cGRhdGVbJ3VwZGF0ZSddKTsKICAgIH0KICAgIGVjaG8gJ1dyb3RlICcuXGRhbm9nXE1hZGVsaW5lUHJvdG9cU2VyaWFsaXphdGlvbjo6c2VyaWFsaXplKCdib3QubWFkZWxpbmUnLCAkYm90KS4nIGJ5dGVzJy5QSFBfRU9MOwp9Cg==';
        $b = 'PD9waHAKLyoKQ29weXJpZ2h0IDIwMTYtMjAxNyBEYW5paWwgR2VudGlsaQooaHR0cHM6Ly9kYW5paWwuaXQpClRoaXMgZmlsZSBpcyBwYXJ0IG9mIE1hZGVsaW5lUHJvdG8uCk1hZGVsaW5lUHJvdG8gaXMgZnJlZSBzb2Z0d2FyZTogeW91IGNhbiByZWRpc3RyaWJ1dGUgaXQgYW5kL29yIG1vZGlmeSBpdCB1bmRlciB0aGUgdGVybXMgb2YgdGhlIEdOVSBBZmZlcm8gR2VuZXJhbCBQdWJsaWMgTGljZW5zZSBhcyBwdWJsaXNoZWQgYnkgdGhlIEZyZWUgU29mdHdhcmUgRm91bmRhdGlvbiwgZWl0aGVyIHZlcnNpb24gMyBvZiB0aGUgTGljZW5zZSwgb3IgKGF0IHlvdXIgb3B0aW9uKSBhbnkgbGF0ZXIgdmVyc2lvbi4KTWFkZWxpbmVQcm90byBpcyBkaXN0cmlidXRlZCBpbiB0aGUgaG9wZSB0aGF0IGl0IHdpbGwgYmUgdXNlZnVsLCBidXQgV0lUSE9VVCBBTlkgV0FSUkFOVFk7IHdpdGhvdXQgZXZlbiB0aGUgaW1wbGllZCB3YXJyYW50eSBvZiBNRVJDSEFOVEFCSUxJVFkgb3IgRklUTkVTUyBGT1IgQSBQQVJUSUNVTEFSIFBVUlBPU0UuClNlZSB0aGUgR05VIEFmZmVybyBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIGZvciBtb3JlIGRldGFpbHMuCllvdSBzaG91bGQgaGF2ZSByZWNlaXZlZCBhIGNvcHkgb2YgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIGFsb25nIHdpdGggTWFkZWxpbmVQcm90by4KSWYgbm90LCBzZWUgPGh0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy8+LgoqLwoKbmFtZXNwYWNlIHN1Ym1lYVxzdWJtZWI7CnVzZSBcZGFub2dcTWFkZWxpbmVQcm90b1xMb2dnZXIgYXMgTG9nZ2VyOwoKY2xhc3MgTWFpbgp7CiAgICBwdWJsaWMgZnVuY3Rpb24gb25HZW5lcmljVXBkYXRlKCR1cGRhdGUpIHsKICAgICAgICAkdGhpcy0+bG9nKCdSZWNlaXZlZCB1cGRhdGUgb2YgdHlwZSAnLiR1cGRhdGVbJ18nXSk7CiAgICB9CiAgICBwdWJsaWMgZnVuY3Rpb24gb25VcGRhdGVOZXdNZXNzYWdlKCR1cGRhdGUpIHsKICAgICAgICBpZiAoJHRoaXMtPm91dGdvaW5nKCR1cGRhdGUpKSByZXR1cm47CiAgICAgICAgJGRlc3RpbmF0aW9uID0gJHVwZGF0ZVsnbWVzc2FnZSddWyd0b19pZCddWydfJ10gPT09ICdwZWVyQ2hhdCcgPyAkdXBkYXRlWydtZXNzYWdlJ11bJ3RvX2lkJ10gOiAkdXBkYXRlWydtZXNzYWdlJ11bJ2Zyb21faWQnXTsKCiAgICAgICAgJHRoaXMtPk1hZGVsaW5lUHJvdG8tPm1lc3NhZ2VzLT5zZW5kTWVzc2FnZShbJ3BlZXInID0+ICRkZXN0aW5hdGlvbiwgJ21lc3NhZ2UnID0+ICJIaSEgSSdtIHBvd2VyZWQgYnkgQE1hZGVsaW5lUHJvdG8sIHdoaWNoIGlzIGEgbGlicmFyeSBjcmVhdGVkIGJ5IFtEYW5paWwgR2VudGlsaV0obWVudGlvbjpAZGFub2dlbnRpbGkpLiIsICdyZXBseV90b19tc2dfaWQnID0+ICR1cGRhdGVbJ21lc3NhZ2UnXVsnaWQnXSwgJ3BhcnNlX21vZGUnID0+ICdNYXJrZG93biddKTsKICAgIH0KICAgIHB1YmxpYyBmdW5jdGlvbiBvblVwZGF0ZU5ld0NoYW5uZWxNZXNzYWdlKCR1cGRhdGUpIHsKICAgICAgICBpZiAoJHRoaXMtPm91dGdvaW5nKCR1cGRhdGUpKSByZXR1cm47CiAgICAgICAgJHRoaXMtPk1hZGVsaW5lUHJvdG8tPm1lc3NhZ2VzLT5zZW5kTWVzc2FnZShbJ3BlZXInID0+ICR1cGRhdGVbJ21lc3NhZ2UnXVsndG9faWQnXSwgJ21lc3NhZ2UnID0+ICJIaSEgSSdtIHBvd2VyZWQgYnkgQE1hZGVsaW5lUHJvdG8sIHdoaWNoIGlzIGEgbGlicmFyeSBjcmVhdGVkIGJ5IFtEYW5paWwgR2VudGlsaV0obWVudGlvbjpAZGFub2dlbnRpbGkpLiIsICdyZXBseV90b19tc2dfaWQnID0+ICR1cGRhdGVbJ21lc3NhZ2UnXVsnaWQnXSwgJ3BhcnNlX21vZGUnID0+ICdNYXJrZG93biddKTsKICAgIH0KICAgIHB1YmxpYyBmdW5jdGlvbiBvblVwZGF0ZU5ld0VuY3J5cHRlZE1lc3NhZ2UoJHVwZGF0ZSkgewogICAgICAgICR0aGlzLT5NYWRlbGluZVByb3RvLT5tZXNzYWdlcy0+c2VuZEVuY3J5cHRlZChbJ3BlZXInID0+ICR1cGRhdGVbJ21lc3NhZ2UnXVsnY2hhdF9pZCddLCAnbWVzc2FnZScgPT4gWydfJyA9PiAnZGVjcnlwdGVkTWVzc2FnZScsICdtZWRpYScgPT4gWydfJyA9PiAnZGVjcnlwdGVkTWVzc2FnZU1lZGlhRW1wdHknXSwgJ3R0bCcgPT4gMTAsICdtZXNzYWdlJyA9PiAiSGkhIEknbSBwb3dlcmVkIGJ5IEBNYWRlbGluZVByb3RvLCB3aGljaCBpcyBhIGxpYnJhcnkgY3JlYXRlZCBieSBbRGFuaWlsIEdlbnRpbGldKG1lbnRpb246QGRhbm9nZW50aWxpKS4iLCAncGFyc2VfbW9kZScgPT4gJ01hcmtkb3duJ11dKTsKICAgIH0KICAgIHB1YmxpYyBmdW5jdGlvbiBvblVwZGF0ZVBob25lQ2FsbCgkdXBkYXRlKSB7CiAgICAgICAgaWYgKGlzX29iamVjdCgkdXBkYXRlWydwaG9uZV9jYWxsJ10pICYmICR1cGRhdGVbJ3Bob25lX2NhbGwnXS0+Z2V0Q2FsbFN0YXRlKCkgPT09IFxkYW5vZ1xNYWRlbGluZVByb3RvXFZvSVA6OkNBTExfU1RBVEVfSU5DT01JTkcpIHsKICAgICAgICAgICAgJHVwZGF0ZVsncGhvbmVfY2FsbCddLT5hY2NlcHQoKS0+cGxheSgnaW5wdXQucmF3JyktPnRoZW4oJ2lucHV0LnJhdycpLT5wbGF5T25Ib2xkKFsnaW5wdXQucmF3J10pLT5zZXRPdXRwdXRGaWxlKCdvdXRwdXQucmF3Jyk7CiAgICAgICAgfQogICAgfQogICAgcHVibGljIGZ1bmN0aW9uIG9uVXBkYXRlSW5saW5lQm90Q2FsbGJhY2tRdWVyeSgkdXBkYXRlKSB7CiAgICAgICAgJHRoaXMtPk1hZGVsaW5lUHJvdG8tPm1lc3NhZ2VzLT5lZGl0SW5saW5lQm90TWVzc2FnZShbJ2lkJyA9PiAkdXBkYXRlWydtc2dfaWQnXSwgJ21lc3NhZ2UnID0+ICdUaGUgcXVlcnkgc3RyaW5nIG9mIHRoaXMgaW5saW5lIG1lc3NhZ2Ugd2FzICcuc3Vic3RyKCR1cGRhdGVbJ2RhdGEnXSwgMCwgLTEpXSk7CiAgICB9CiAgICBwdWJsaWMgZnVuY3Rpb24gb25VcGRhdGVCb3RJbmxpbmVRdWVyeSgkdXBkYXRlKSB7CiAgICAgICAgJHRvc2V0ID0gWwogICAgICAgICAgICAncXVlcnlfaWQnID0+ICR1cGRhdGVbJ3F1ZXJ5X2lkJ10sCiAgICAgICAgICAgICdyZXN1bHRzJyA9PiBbCiAgICAgICAgICAgICAgICBbCiAgICAgICAgICAgICAgICAgICAgJ18nID0+ICdpbnB1dEJvdElubGluZVJlc3VsdCcsCiAgICAgICAgICAgICAgICAgICAgJ2lkJyA9PiAnc3RyaW5nJywKICAgICAgICAgICAgICAgICAgICAndGl0bGUnID0+ICdSZXN1bHQnLAogICAgICAgICAgICAgICAgICAgICd0eXBlJyA9PiAnYXJ0aWNsZScsCiAgICAgICAgICAgICAgICAgICAgJ3NlbmRfbWVzc2FnZScgPT4gWwogICAgICAgICAgICAgICAgICAgICAgICAnXycgPT4gJ2lucHV0Qm90SW5saW5lTWVzc2FnZVRleHQnLAogICAgICAgICAgICAgICAgICAgICAgICAndGV4dCcgPT4gIkhpISBJJ20gcG93ZXJlZCBieSBATWFkZWxpbmVQcm90bywgd2hpY2ggaXMgYSBsaWJyYXJ5IGNyZWF0ZWQgYnkgW0RhbmlpbCBHZW50aWxpXShtZW50aW9uOkBkYW5vZ2VudGlsaSkuIiwKICAgICAgICAgICAgICAgICAgICAgICAgJ3BhcnNlX21vZGUnID0+ICdNYXJrZG93bicsCiAgICAgICAgICAgICAgICAgICAgICAgICdyZXBseV9tYXJrdXAnID0+IFsKICAgICAgICAgICAgICAgICAgICAgICAgICAgICdfJyA9PiAncmVwbHlJbmxpbmVNYXJrdXAnLAogICAgICAgICAgICAgICAgICAgICAgICAgICAgJ3Jvd3MnID0+IFsKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdfJyA9PiAna2V5Ym9hcmRCdXR0b25Sb3cnLAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnYnV0dG9ucycgPT4gWwogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgWwogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdfJyA9PiAna2V5Ym9hcmRCdXR0b25DYWxsYmFjaycsCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ3RleHQnID0+ICdUcnkgbWUhJywKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZGF0YScgPT4gJHVwZGF0ZVsncXVlcnknXS4nYScKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIF0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgXQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIF0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIF0KICAgICAgICAgICAgICAgICAgICAgICAgXQogICAgICAgICAgICAgICAgICAgIF0KICAgICAgICAgICAgICAgIF0KICAgICAgICAgICAgXSwKICAgICAgICAgICAgJ2NhY2hlX3RpbWUnID0+IDAKICAgICAgICBdOwogICAgICAgICR0aGlzLT5NYWRlbGluZVByb3RvLT5tZXNzYWdlcy0+c2V0SW5saW5lQm90UmVzdWx0cygkdG9zZXQpOwogICAgfQoKCgoKCgoKCiAgICAKICAgIHB1YmxpYyBmdW5jdGlvbiBsb2coJG1lc3NhZ2UsICRsZXZlbCA9IFxkYW5vZ1xNYWRlbGluZVByb3RvXExvZ2dlcjo6V0FSTklORykgewogICAgICAgIFxkYW5vZ1xNYWRlbGluZVByb3RvXExvZ2dlcjo6bG9nKCRtZXNzYWdlLCAkbGV2ZWwpOwogICAgfQogICAgcHVibGljICRNYWRlbGluZVByb3RvOwogICAgcHVibGljIGZ1bmN0aW9uIF9fY29uc3RydWN0KCRNYWRlbGluZVByb3RvKSB7CiAgICAgICAgJHRoaXMtPk1hZGVsaW5lUHJvdG8gPSAkTWFkZWxpbmVQcm90bzsKICAgIH0KICAgIHB1YmxpYyBmdW5jdGlvbiBvdXRnb2luZygkdXBkYXRlKSB7IHJldHVybiBpc3NldCgkdXBkYXRlWydtZXNzYWdlJ11bJ291dCddKSAmJiAkdXBkYXRlWydtZXNzYWdlJ11bJ291dCddOyB9CiAgICBwdWJsaWMgZnVuY3Rpb24gX19jYWxsKCRtZXRob2QsICRhcmdzKSB7CiAgICAgICAgaWYgKHN0cnBvcygkbWV0aG9kLCAnb24nKSA9PT0gMCkgewogICAgICAgICAgICByZXR1cm4gJHRoaXMtPm9uR2VuZXJpY1VwZGF0ZSguLi4kYXJncyk7CiAgICAgICAgfQogICAgICAgIHJldHVybiAkdGhpcy0+TWFkZWxpbmVQcm90by0+eyRtZXRob2R9KC4uLiRhcmdzKTsKICAgIH0KICAgIHB1YmxpYyBmdW5jdGlvbiBfX2dldCgkd2hhdCkgewogICAgICAgIHJldHVybiBpc3NldCgkdGhpcy0+TWFkZWxpbmVQcm90by0+eyR3aGF0fSkgPyAkdGhpcy0+TWFkZWxpbmVQcm90by0+eyR3aGF0fSA6ICR0aGlzLT57JHdoYXR9OwogICAgfQoKCn0K';
        echo 'Storing files...' . PHP_EOL;
        file_put_contents($project . '/bot.php', str_replace(['submea', 'submeb'], [$nick, $project], base64_decode($a)));
        file_put_contents($project . '/src/' . $nick . '/' . $project . '/Main.php', str_replace(['submea', 'submeb'], [$nick, $project], base64_decode($b)));
        file_put_contents($project . '/input.raw', file_get_contents('https://github.com/danog/MadelineProto/blob/master/input.raw?raw=true'));
        chdir($project);
        shell_exec('composer update');
        echo "Done!\n\nFeel free to edit " . $project . "/composer.json\n\nYour new MadelineProto bot is located in " . $project . '/src/' . $nick . '/' . $project . "/Main.php\n\n================\n\nRun the following commands to start the bot:\n\ncd " . $project . ' and then php bot.php' . PHP_EOL;
        exit;
    }
    private function new_session()
    {
        if (readline('Do you want to create a new session? (y/n) ') == 'y') {
            $MadelineProto = new \danog\MadelineProto\API(json_decode($this->settings, true));
            if (readline('Do you want to create a bot or a userbot? (bot/userbot) ') == 'bot') {
                $MadelineProto->bot_login(readline('Enter the token of the bot: '));
            } else {
                $sentCode = $MadelineProto->phone_login(readline('Enter your phone number: '));
                \danog\MadelineProto\Logger::log([$sentCode], \danog\MadelineProto\Logger::NOTICE);
                echo 'Enter the code you received: ';
                $code = fgets(STDIN, (isset($sentCode['type']['length']) ? $sentCode['type']['length'] : 5) + 1);
                $authorization = $MadelineProto->complete_phone_login($code);
                \danog\MadelineProto\Logger::log([$authorization], \danog\MadelineProto\Logger::NOTICE);
                if ($authorization['_'] === 'account.noPassword') {
                    throw new \danog\MadelineProto\Exception('2FA is enabled but no password is set!');
                }
                if ($authorization['_'] === 'account.password') {
                    \danog\MadelineProto\Logger::log(['2FA is enabled'], \danog\MadelineProto\Logger::NOTICE);
                    $authorization = $MadelineProto->complete_2fa_login(readline('Please enter your password (hint ' . $authorization['hint'] . '): '));
                }
                if ($authorization['_'] === 'account.needSignup') {
                    \danog\MadelineProto\Logger::log(['Registering new user'], \danog\MadelineProto\Logger::NOTICE);
                    $authorization = $MadelineProto->complete_signup(readline('Please enter your first name: '), readline('Please enter your last name (can be empty): '));
                }
            }
            $session_file = readline('Enter the name for the session file: ');
            echo 'Serializing the session...' . PHP_EOL;
            \danog\MadelineProto\Serialization::serialize($session_file, $MadelineProto);
            echo 'Serialized the session' . PHP_EOL;
            while (1) {
                try {
                    $r = readline('> ');
                    if (empty($r)) {
                        continue;
                    }
                    switch (strtolower($r)) {
                case 'version':
                case '-v':
                    echo 'MadelineProto CLI Version ' . $this->MADELINECLIENT_VERSION . "\nDeveloped by Hearot, MadelineProto by Daniil Gentili.\nCopyright 2017" . PHP_EOL;
                    continue 2;
                break;
                case 'exit':
                    echo 'Bye!' . PHP_EOL;
                    exit;
                break;
                }
                    $l = explode(' ', $r);
                    try {
                        $method = $MadelineProto->API->methods->find_by_method($l[0]);
                    } catch (Exception $e) {
                        continue;
                    }
                    if ($method and count($l) == 1) {
                        $help_string = '';
                        foreach ($method['params'] as $method_param) {
                            $help_string .= $method_param['name'] . ' ';
                        }
                        echo 'Help: ' . $l[0] . ' ' . $help_string . PHP_EOL;
                        continue;
                    } elseif ($method) {
                        $node = explode('.', $l[0]);
                        $param_array = array();
                        $i = 1;
                        foreach ($method['params'] as $method_param) {
                            $param_array[$method_param['name']] = $l[$i++];
                        }
                        var_dump($MadelineProto->{$node[0]}->{$node[1]}($param_array));
                        \danog\MadelineProto\Serialization::serialize($session_file, $MadelineProto);
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
        } else {
            echo 'Bye!' . PHP_EOL;
            exit;
        }
    }
}
