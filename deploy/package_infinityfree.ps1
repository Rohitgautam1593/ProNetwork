param(
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path,
    [string]$OutputDir = $PSScriptRoot
)

$ErrorActionPreference = 'Stop'

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$ProjectRoot = (Get-Item -LiteralPath $ProjectRoot).FullName
$OutputDir = (New-Item -ItemType Directory -Force -Path $OutputDir).FullName

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$zipPath = Join-Path $OutputDir "pronetwork-infinityfree-$timestamp.zip"
$manifestPath = Join-Path $OutputDir "pronetwork-infinityfree-$timestamp.manifest.txt"

$includeRoots = @(
    '.htaccess',
    'index.php',
    'composer.json',
    'composer.lock',
    'DEPLOY_INFINITYFREE.md',
    'admin',
    'app',
    'company',
    'database',
    'public',
    'user',
    'vendor'
)

$excludedRelativePaths = @(
    'app/config/config.local.php',
    'composer.phar',
    'test_login.ps1',
    'nixpacks.toml',
    'public/uploads/covers/1778245103_IMG_20260103_150817.jpg',
    'public/uploads/posts/1778244209_1000059971.jpg.jpeg',
    'public/uploads/posts/1778764805_a1524ddba2963071.mp4',
    'public/uploads/posts/1778764828_93ff2a3031e82283.mp4',
    'public/uploads/posts/1779112058_aa218ff6ac9024fb.jpg',
    'public/uploads/posts/1779112261_f5d6c0b6ec31aa69.jpg'
)

function Convert-ToZipPath {
    param([string]$Path)
    return $Path.Replace('\', '/')
}

function Test-DeployExcluded {
    param([string]$RelativePath)

    $zipRelativePath = Convert-ToZipPath $RelativePath

    foreach ($excludedPath in $excludedRelativePaths) {
        if ($zipRelativePath.Equals($excludedPath, [StringComparison]::OrdinalIgnoreCase)) {
            return $true
        }
    }

    return $false
}

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)
$entries = New-Object System.Collections.Generic.List[string]

try {
    foreach ($includeRoot in $includeRoots) {
        $absoluteRoot = Join-Path $ProjectRoot $includeRoot
        if (-not (Test-Path -LiteralPath $absoluteRoot)) {
            continue
        }

        $item = Get-Item -LiteralPath $absoluteRoot -Force
        $itemsToAdd = if ($item.PSIsContainer) {
            Get-ChildItem -LiteralPath $item.FullName -Recurse -Force
        } else {
            @($item)
        }

        foreach ($entryItem in $itemsToAdd) {
            $relativePath = $entryItem.FullName.Substring($ProjectRoot.Length).TrimStart('\', '/')
            $zipRelativePath = Convert-ToZipPath $relativePath

            if (Test-DeployExcluded $zipRelativePath) {
                continue
            }

            if ($entryItem.PSIsContainer) {
                $directoryEntry = $zipRelativePath.TrimEnd('/') + '/'
                if (-not $entries.Contains($directoryEntry)) {
                    [void]$zip.CreateEntry($directoryEntry)
                    [void]$entries.Add($directoryEntry)
                }
                continue
            }

            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                $zip,
                $entryItem.FullName,
                $zipRelativePath,
                [System.IO.Compression.CompressionLevel]::Optimal
            ) | Out-Null
            [void]$entries.Add($zipRelativePath)
        }
    }
}
finally {
    $zip.Dispose()
}

$entries |
    Sort-Object |
    Set-Content -LiteralPath $manifestPath -Encoding ASCII

$blockedPatterns = @(
    '^app/config/config\.local\.php$',
    '^composer\.phar$',
    '^test_login\.ps1$',
    '^nixpacks\.toml$',
    '^deploy/',
    '^blog/',
    '^\.git/'
)

$blockedEntries = $entries | Where-Object {
    $entry = $_
    $blockedPatterns | Where-Object { $entry -match $_ }
}

if ($blockedEntries) {
    throw "Deployment package contains excluded entries: $($blockedEntries -join ', ')"
}

$requiredEntries = @(
    '.htaccess',
    'index.php',
    'app/config/config.php',
    'app/config/config.infinityfree.example.php',
    'database/infinityfree_schema.sql',
    'public/.htaccess',
    'public/index.php',
    'vendor/autoload.php',
    'user/backend/controllers/PagesController.php',
    'user/frontend/views/pages/about_us.php'
)

$missingEntries = $requiredEntries | Where-Object { -not $entries.Contains($_) }
if ($missingEntries) {
    throw "Deployment package is missing required entries: $($missingEntries -join ', ')"
}

$zipInfo = Get-Item -LiteralPath $zipPath
Write-Host "Created $($zipInfo.FullName)"
Write-Host "Entries: $($entries.Count)"
Write-Host "Size: $([Math]::Round($zipInfo.Length / 1MB, 2)) MB"
Write-Host "Manifest: $manifestPath"

