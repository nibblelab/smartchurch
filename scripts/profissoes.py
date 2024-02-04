#!/usr/bin/python3

from read_json import ReadJSON
import random
from time import gmtime, strftime

def getRandomSeq():
    seq = ''
    for i in range(0,10):
        seq += '' + str(random.randint(0,9))
    return seq

def getId():
    id = strftime("%Y%m%d%H%M%S", gmtime()) + getRandomSeq()
    return id

json = ReadJSON()
json.load("lista1.json")

profissoes = []
for p in json.getData()['profissoes']:
    if p not in profissoes:
        profissoes.append(p)

json.load("lista2.json")
for p in json.getData()['profissoes']:
    if p not in profissoes:
        profissoes.append(p)

profissoes = sorted(profissoes)

for p in profissoes:
    print("INSERT INTO profissoes VALUES('%s','%s');" %(getId(),p))
