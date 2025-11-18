from pyasn1.codec.ber import decoder
import json

# Função para converter os dados do arquivo ASN.1 em JSON
def bu_to_json(asn1_file_path, asn1_spec_path, json_output_path):
    # Abre o arquivo ASN.1 para ler a estrutura
    with open(asn1_file_path, 'rb') as asn1_file:
        bu_data, _ = decoder.decode(asn1_file.read())

        # Converte a estrutura ASN.1 para um dicionário Python
        bu_dict = asn1_to_dict(bu_data)
        
        # Salva o dicionário em formato JSON
        with open(json_output_path, 'w') as json_file:
            json.dump(bu_dict, json_file, indent=4)

# Função para converter a estrutura ASN.1 em um dicionário Python
def asn1_to_dict(asn1_data):
    # Verifica se os dados são um dicionário
    if isinstance(asn1_data, dict):
        return {key: asn1_to_dict(value) for key, value in asn1_data.items()}
    # Verifica se os dados são uma lista
    elif isinstance(asn1_data, list):
        return [asn1_to_dict(item) for item in asn1_data]
    # Retorna os valores em string
    else:
        return str(asn1_data)

# Caminhos dos arquivos
asn1_file_path = 'C:\\xampp8\\htdocs\\siseleitoral\\sn1\\downloads\\o00452ac0139200010003-bu.dat'
asn1_spec_path = 'C:\\xampp8\\htdocs\\siseleitoral\\sn1\\python\\spec2024\\bu.asn1'
json_output_path = 'C:\\xampp8\\htdocs\\siseleitoral\\sn1\\json\\bu_output.json'

# Executa a conversão
bu_to_json(asn1_file_path, asn1_spec_path, json_output_path)
