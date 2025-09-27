@echo off
REM Run tests first to ensure everything is working
call bin\test.cmd

REM If tests passed (errorlevel 0), proceed with publishing
if %errorlevel% equ 0 (
    echo Tests passed. Proceeding with publish...
    
    REM Create a new git tag based on the version in composer.json
    for /f "tokens=*" %%i in ('docker compose run --rm composer config version') do set VERSION=%%i
    
    git tag -a v%VERSION% -m "Version %VERSION%"
    git push origin v%VERSION%
    
    echo Tagged and pushed version v%VERSION%
    echo Now visit https://packagist.org/packages/submit
    echo and submit the GitHub repository URL: https://github.com/lotcz/zavadil-php-common
) else (
    echo Tests failed. Fix the issues before publishing.
    exit /b 1
)