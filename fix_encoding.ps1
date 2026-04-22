param()
$filePath = Join-Path $PSScriptRoot 'app\Views\frontend\home.php'
$bytes = [System.IO.File]::ReadAllBytes($filePath)

# The corrupted sequence bytes for em-dash: C3 A2 E2 82 AC E2 80 94  (or similar double-encoding)
# From inspection: 195 162 226 130 172 226 128 157
# We want to replace with: E2 80 94 (UTF-8 em-dash = 226 128 148) or just a plain hyphen
# Let's replace with U+2014 EM DASH = bytes E2 80 94 = 226 128 148

$broken = [byte[]]@(195, 162, 226, 130, 172, 226, 128, 157)
$replacement = [byte[]]@(226, 128, 148)  # U+2014 EM DASH

# Convert to list for manipulation
$list = New-Object System.Collections.Generic.List[byte]
$list.AddRange($bytes)

$i = 0
$result = New-Object System.Collections.Generic.List[byte]
while ($i -lt $bytes.Length) {
    # Check if broken sequence starts here
    if ($i + $broken.Length - 1 -lt $bytes.Length) {
        $match = $true
        for ($j = 0; $j -lt $broken.Length; $j++) {
            if ($bytes[$i + $j] -ne $broken[$j]) { $match = $false; break }
        }
        if ($match) {
            $result.AddRange($replacement)
            $i += $broken.Length
            continue
        }
    }
    $result.Add($bytes[$i])
    $i++
}

[System.IO.File]::WriteAllBytes($filePath, $result.ToArray())
Write-Host "Done. File size: $($result.Count) bytes"

# Verify
$check = [System.IO.File]::ReadAllText($filePath, [System.Text.Encoding]::UTF8)
$idx = $check.IndexOf('coding')
if ($idx -ge 0) { Write-Host "Verify: $($check.Substring($idx, 35))" }
