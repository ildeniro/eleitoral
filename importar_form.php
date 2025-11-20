<!DOCTYPE html>
<html>
    <head><title>Importar Candidatos</title></head>
    <body style="font-family: system-ui; background:#0f172a; color:white; padding:50px;">
        <h1>Importar Candidatos TSE</h1>
        <form method="POST" enctype="multipart/form-data" action="importar_candidatos">
            <p><label><strong>Ano:</strong></label><br>
                <select name="ano" required style="padding:10px; font-size:18px;">
                    <option value="2024">2024</option>
                    <option value="2022">2022</option>
                </select></p>

            <p><label><strong>Arquivo CSV (consulta_cand_XXXX_AC.csv):</strong></label><br>
                <input type="file" name="csv" accept=".csv" required style="padding:10px;"></p>

            <button type="submit" style="padding:15px 30px; font-size:18px; background:#0066cc; color:white; border:none; border-radius:8px;">
                IMPORTAR AGORA
            </button>
        </form>
    </body>
</html>