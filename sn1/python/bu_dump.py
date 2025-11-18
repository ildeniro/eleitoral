import argparse
import logging
import os
import sys
import json
import asn1tools

def espacos(profundidade: int):
    return ".   " * profundidade

def valor_membro(membro):
    if isinstance(membro, (bytes, bytearray)):
        return bytes(membro).hex()
    return membro

def process_dict(entidade: dict):
    processed = {}
    for key, membro in entidade.items():
        if isinstance(membro, dict):
            processed[key] = process_dict(membro)
        elif isinstance(membro, list):
            processed[key] = [process_dict(item) if isinstance(item, dict) else valor_membro(item) for item in membro]
        else:
            processed[key] = valor_membro(membro)
    return processed

def processa_bu(asn1_paths: list, bu_path: str):
    conv = asn1tools.compile_files(asn1_paths, codec="ber")
    with open(bu_path, "rb") as file:
        envelope_encoded = bytearray(file.read())
    envelope_decoded = conv.decode("EntidadeEnvelopeGenerico", envelope_encoded)
    bu_encoded = envelope_decoded.pop("conteudo")  # Remove o conteúdo para não imprimir como array de bytes
    bu_decoded = conv.decode("EntidadeBoletimUrna", bu_encoded)

    # Processa dicionários para JSON
    envelope_decoded = process_dict(envelope_decoded)
    bu_decoded = process_dict(bu_decoded)

    # Salva o JSON na mesma pasta do script
    base_dir = os.path.dirname(os.path.abspath(__file__))
    json_file_path = os.path.join(base_dir, "bu_output.json")
    with open(json_file_path, "w", encoding="utf-8") as json_file:
        json.dump({
            "EntidadeEnvelopeGenerico": envelope_decoded,
            "EntidadeBoletimUrna": bu_decoded
        }, json_file, indent=4, ensure_ascii=False)

    logging.info(f"JSON salvo em: {json_file_path}")

def main():
    parser = argparse.ArgumentParser(
        description="Converte um Boletim de Urna (BU) da Urna Eletrônica (UE) e gera um JSON",
        formatter_class=argparse.RawTextHelpFormatter)
    parser.add_argument("-a", "--asn1", nargs="+", required=True,
                        help="Caminho para o arquivo de especificação asn1 do BU")
    parser.add_argument("-b", "--bu", type=str, required=True,
                        help="Caminho para o arquivo de BU originado na UE")
    parser.add_argument("--debug", action="store_true", help="ativa o modo DEBUG do log")

    args = parser.parse_args()

    bu_path = args.bu
    asn1_paths = args.asn1
    level = logging.DEBUG if args.debug else logging.INFO
    logging.basicConfig(level=level, format="%(asctime)s - %(levelname)s - %(message)s")

    logging.info("Converte %s com as especificações %s", bu_path, asn1_paths)
    if not os.path.exists(bu_path):
        logging.error("Arquivo do BU (%s) não encontrado", bu_path)
        sys.exit(-1)
    for asn1_path in asn1_paths:
        if not os.path.exists(asn1_path):
            logging.error("Arquivo de especificação do BU (%s) não encontrado", asn1_path)
            sys.exit(-1)

    processa_bu(asn1_paths, bu_path)

if __name__ == "__main__":
    main()
