-- Migration Script: Migrate RFK to RFK V2
-- This script migrates data from rfk and rfk_detail tables to rfk_v2 and rfk_v2_detail tables
--
-- IMPORTANT: Review and adjust this script based on your actual data structure before running
--
-- Usage:
-- 1. Review the script
-- 2. Make a backup of your database
-- 3. Test on a development/staging environment first
-- 4. Run this script on production

-- Step 1: Migrate RFK to RFK V2
-- Assuming month format is "01", "02", etc. or "01-2024"
-- Adjust the date parsing logic based on your actual month format

INSERT INTO rfk_v2 (id_opd, month, year, status, created_at, updated_at)
SELECT
    id_opd,
    -- Extract month (assuming format like "01" or "01-2024")
    CASE
        WHEN month LIKE '%-%' THEN LPAD(SUBSTRING_INDEX(month, '-', 1), 2, '0')
        ELSE LPAD(month, 2, '0')
    END AS month,
    -- Extract year (assuming format like "01-2024" or use created_at year)
    CASE
        WHEN month LIKE '%-%' THEN SUBSTRING_INDEX(month, '-', -1)
        ELSE YEAR(created_at)
    END AS year,
    status,
    created_at,
    updated_at
FROM rfk
WHERE NOT EXISTS (
    -- Skip if already exists
    SELECT 1 FROM rfk_v2 r2
    WHERE r2.id_opd = rfk.id_opd
    AND r2.month = CASE
        WHEN rfk.month LIKE '%-%' THEN LPAD(SUBSTRING_INDEX(rfk.month, '-', 1), 2, '0')
        ELSE LPAD(rfk.month, 2, '0')
    END
    AND r2.year = CASE
        WHEN rfk.month LIKE '%-%' THEN SUBSTRING_INDEX(rfk.month, '-', -1)
        ELSE YEAR(rfk.created_at)
    END
);

-- Step 2: Migrate RFK Detail to RFK V2 Detail
-- First, create OPD level entries
INSERT INTO rfk_v2_detail (
    id_rfk, parent_id, type, code, uraian, year,
    pagu_anggaran, bulan_lalu, bulan_ini, sd_bulan_ini, sisa_anggaran,
    angkas, angkas_prosentase, keuangan, fisik, fisik_rupiah, keterangan,
    created_at, updated_at
)
SELECT
    r2.id AS id_rfk,
    NULL AS parent_id,
    'opd' AS type,
    rd.code,
    o.name AS uraian,
    r2.year,
    COALESCE(rd.pagu_anggaran, 0) AS pagu_anggaran,
    COALESCE(rd.bulan_lalu, 0) AS bulan_lalu,
    COALESCE(rd.bulan_ini, 0) AS bulan_ini,
    COALESCE(rd.sd_bulan_ini, 0) AS sd_bulan_ini,
    COALESCE(rd.sisa_anggaran, 0) AS sisa_anggaran,
    COALESCE(rd.angkas, 0) AS angkas,
    COALESCE(rd.angkas_prosentase, 0) AS angkas_prosentase,
    COALESCE(rd.keuangan, 0) AS keuangan,
    COALESCE(rd.fisik, 0) AS fisik,
    COALESCE(rd.fisik, 0) * COALESCE(rd.pagu_anggaran, 0) / 100 AS fisik_rupiah,
    rd.keterangan,
    rd.created_at,
    rd.updated_at
FROM rfk_detail rd
INNER JOIN rfk r ON rd.id_rfk = r.id
INNER JOIN rfk_v2 r2 ON r2.id_opd = r.id_opd
    AND r2.month = CASE
        WHEN r.month LIKE '%-%' THEN LPAD(SUBSTRING_INDEX(r.month, '-', 1), 2, '0')
        ELSE LPAD(r.month, 2, '0')
    END
    AND r2.year = CASE
        WHEN r.month LIKE '%-%' THEN SUBSTRING_INDEX(r.month, '-', -1)
        ELSE YEAR(r.created_at)
    END
INNER JOIN opd o ON r.id_opd = o.id
WHERE NOT EXISTS (
    SELECT 1 FROM rfk_v2_detail r2d
    WHERE r2d.id_rfk = r2.id
    AND r2d.code = rd.code
)
-- Group by to get OPD level (you may need to adjust this based on your code structure)
GROUP BY r2.id, rd.code, o.name, r2.year, rd.pagu_anggaran, rd.bulan_lalu, rd.bulan_ini,
    rd.sd_bulan_ini, rd.sisa_anggaran, rd.angkas, rd.angkas_prosentase, rd.keuangan,
    rd.fisik, rd.keterangan, rd.created_at, rd.updated_at
HAVING LENGTH(rd.code) - LENGTH(REPLACE(rd.code, '.', '')) <= 6; -- Adjust based on your code structure

-- Step 3: Create Program level entries (adjust code pattern matching)
INSERT INTO rfk_v2_detail (
    id_rfk, parent_id, type, code, uraian, year,
    pagu_anggaran, bulan_lalu, bulan_ini, sd_bulan_ini, sisa_anggaran,
    angkas, angkas_prosentase, keuangan, fisik, fisik_rupiah, keterangan,
    created_at, updated_at
)
SELECT
    r2.id AS id_rfk,
    opd_detail.id AS parent_id,
    'program' AS type,
    rd.code,
    rd.uraian AS uraian, -- Get uraian from rfk_detail
    r2.year,
    COALESCE(rd.pagu_anggaran, 0),
    COALESCE(rd.bulan_lalu, 0),
    COALESCE(rd.bulan_ini, 0),
    COALESCE(rd.sd_bulan_ini, 0),
    COALESCE(rd.sisa_anggaran, 0),
    COALESCE(rd.angkas, 0),
    COALESCE(rd.angkas_prosentase, 0),
    COALESCE(rd.keuangan, 0),
    COALESCE(rd.fisik, 0),
    COALESCE(rd.fisik, 0) * COALESCE(rd.pagu_anggaran, 0) / 100,
    rd.keterangan,
    rd.created_at,
    rd.updated_at
FROM rfk_detail rd
INNER JOIN rfk r ON rd.id_rfk = r.id
INNER JOIN rfk_v2 r2 ON r2.id_opd = r.id_opd
    AND r2.month = CASE
        WHEN r.month LIKE '%-%' THEN LPAD(SUBSTRING_INDEX(r.month, '-', 1), 2, '0')
        ELSE LPAD(r.month, 2, '0')
    END
    AND r2.year = CASE
        WHEN r.month LIKE '%-%' THEN SUBSTRING_INDEX(r.month, '-', -1)
        ELSE YEAR(r.created_at)
    END
INNER JOIN rfk_v2_detail opd_detail ON opd_detail.id_rfk = r2.id
    AND opd_detail.type = 'opd'
    AND rd.code LIKE CONCAT(opd_detail.code, '%')
WHERE NOT EXISTS (
    SELECT 1 FROM rfk_v2_detail r2d
    WHERE r2d.id_rfk = r2.id
    AND r2d.code = rd.code
)
-- Adjust this condition based on your code structure
AND LENGTH(rd.code) - LENGTH(REPLACE(rd.code, '.', '')) BETWEEN 7 AND 9;

-- Step 4: Create Kegiatan level entries
INSERT INTO rfk_v2_detail (
    id_rfk, parent_id, type, code, uraian, year,
    pagu_anggaran, bulan_lalu, bulan_ini, sd_bulan_ini, sisa_anggaran,
    angkas, angkas_prosentase, keuangan, fisik, fisik_rupiah, keterangan,
    created_at, updated_at
)
SELECT
    r2.id AS id_rfk,
    program_detail.id AS parent_id,
    'kegiatan' AS type,
    rd.code,
    rd.uraian AS uraian,
    r2.year,
    COALESCE(rd.pagu_anggaran, 0),
    COALESCE(rd.bulan_lalu, 0),
    COALESCE(rd.bulan_ini, 0),
    COALESCE(rd.sd_bulan_ini, 0),
    COALESCE(rd.sisa_anggaran, 0),
    COALESCE(rd.angkas, 0),
    COALESCE(rd.angkas_prosentase, 0),
    COALESCE(rd.keuangan, 0),
    COALESCE(rd.fisik, 0),
    COALESCE(rd.fisik, 0) * COALESCE(rd.pagu_anggaran, 0) / 100,
    rd.keterangan,
    rd.created_at,
    rd.updated_at
FROM rfk_detail rd
INNER JOIN rfk r ON rd.id_rfk = r.id
INNER JOIN rfk_v2 r2 ON r2.id_opd = r.id_opd
    AND r2.month = CASE
        WHEN r.month LIKE '%-%' THEN LPAD(SUBSTRING_INDEX(r.month, '-', 1), 2, '0')
        ELSE LPAD(r.month, 2, '0')
    END
    AND r2.year = CASE
        WHEN r.month LIKE '%-%' THEN SUBSTRING_INDEX(r.month, '-', -1)
        ELSE YEAR(r.created_at)
    END
INNER JOIN rfk_v2_detail program_detail ON program_detail.id_rfk = r2.id
    AND program_detail.type = 'program'
    AND rd.code LIKE CONCAT(program_detail.code, '%')
    AND rd.code != program_detail.code
WHERE NOT EXISTS (
    SELECT 1 FROM rfk_v2_detail r2d
    WHERE r2d.id_rfk = r2.id
    AND r2d.code = rd.code
)
-- Adjust this condition based on your code structure
AND LENGTH(rd.code) - LENGTH(REPLACE(rd.code, '.', '')) BETWEEN 10 AND 11;

-- Step 5: Create Sub Kegiatan level entries (all remaining)
INSERT INTO rfk_v2_detail (
    id_rfk, parent_id, type, code, uraian, year,
    pagu_anggaran, bulan_lalu, bulan_ini, sd_bulan_ini, sisa_anggaran,
    angkas, angkas_prosentase, keuangan, fisik, fisik_rupiah, keterangan,
    created_at, updated_at
)
SELECT
    r2.id AS id_rfk,
    kegiatan_detail.id AS parent_id,
    'sub_kegiatan' AS type,
    rd.code,
    rd.uraian AS uraian,
    r2.year,
    COALESCE(rd.pagu_anggaran, 0),
    COALESCE(rd.bulan_lalu, 0),
    COALESCE(rd.bulan_ini, 0),
    COALESCE(rd.sd_bulan_ini, 0),
    COALESCE(rd.sisa_anggaran, 0),
    COALESCE(rd.angkas, 0),
    COALESCE(rd.angkas_prosentase, 0),
    COALESCE(rd.keuangan, 0),
    COALESCE(rd.fisik, 0),
    COALESCE(rd.fisik, 0) * COALESCE(rd.pagu_anggaran, 0) / 100,
    rd.keterangan,
    rd.created_at,
    rd.updated_at
FROM rfk_detail rd
INNER JOIN rfk r ON rd.id_rfk = r.id
INNER JOIN rfk_v2 r2 ON r2.id_opd = r.id_opd
    AND r2.month = CASE
        WHEN r.month LIKE '%-%' THEN LPAD(SUBSTRING_INDEX(r.month, '-', 1), 2, '0')
        ELSE LPAD(r.month, 2, '0')
    END
    AND r2.year = CASE
        WHEN r.month LIKE '%-%' THEN SUBSTRING_INDEX(r.month, '-', -1)
        ELSE YEAR(r.created_at)
    END
INNER JOIN rfk_v2_detail kegiatan_detail ON kegiatan_detail.id_rfk = r2.id
    AND kegiatan_detail.type = 'kegiatan'
    AND rd.code LIKE CONCAT(kegiatan_detail.code, '%')
    AND rd.code != kegiatan_detail.code
WHERE NOT EXISTS (
    SELECT 1 FROM rfk_v2_detail r2d
    WHERE r2d.id_rfk = r2.id
    AND r2d.code = rd.code
)
-- All remaining entries
AND LENGTH(rd.code) - LENGTH(REPLACE(rd.code, '.', '')) >= 12;

-- Verification Query
-- Check migration results
SELECT
    'RFK V2 Records' AS table_name,
    COUNT(*) AS count
FROM rfk_v2
UNION ALL
SELECT
    'RFK V2 Detail Records' AS table_name,
    COUNT(*) AS count
FROM rfk_v2_detail
UNION ALL
SELECT
    'RFK V2 Detail by Type' AS table_name,
    COUNT(*) AS count
FROM rfk_v2_detail
GROUP BY type;

-- Check for orphaned records (details without parent match)
SELECT
    r2d.id,
    r2d.code,
    r2d.type,
    r2d.parent_id,
    CASE
        WHEN r2d.parent_id IS NOT NULL AND NOT EXISTS (
            SELECT 1 FROM rfk_v2_detail p WHERE p.id = r2d.parent_id
        ) THEN 'MISSING PARENT'
        ELSE 'OK'
    END AS status
FROM rfk_v2_detail r2d
WHERE r2d.parent_id IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM rfk_v2_detail p WHERE p.id = r2d.parent_id
);

