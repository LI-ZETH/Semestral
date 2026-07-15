$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$parentPath = Split-Path -Parent $projectRoot
$releaseName = 'TrackiT_Entrega_Final'
$tempPath = Join-Path $parentPath $releaseName
$zipPath = Join-Path $parentPath ($releaseName + '.zip')

if (Test-Path $tempPath) {
    Remove-Item $tempPath -Recurse -Force
}

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

Copy-Item $projectRoot $tempPath -Recurse -Force

$pathsToRemove = @(
    (Join-Path $tempPath '.git'),
    (Join-Path $tempPath '.idea'),
    (Join-Path $tempPath '.vscode'),
    (Join-Path $tempPath 'config\database.php'),
    (Join-Path $tempPath 'config\crypto.php')
)

foreach ($path in $pathsToRemove) {
    if (Test-Path $path) {
        Remove-Item $path -Recurse -Force
    }
}

$patternsToRemove = @(
    (Join-Path $tempPath 'storage\keys\*.pem'),
    (Join-Path $tempPath 'storage\logs\*.log'),
    (Join-Path $tempPath 'storage\qrcodes\*.svg')
)

foreach ($pattern in $patternsToRemove) {
    Get-ChildItem $pattern -ErrorAction SilentlyContinue |
        Remove-Item -Force
}

Compress-Archive -Path $tempPath -DestinationPath $zipPath -Force
Remove-Item $tempPath -Recurse -Force

Write-Host "Entrega creada correctamente:" -ForegroundColor Green
Write-Host $zipPath
