
# Biblioteca de integração com a API de localidades do IBGE

A especificação da API pode ser encontrada em https://servicodados.ibge.gov.br/api/docs/localidades?versao=1

# Sumário

1. [Pré requisitos](#pré-requisitos)
2. [Inicialização](#inicialização)
3. [Regiões](#regiões)
4. [UFs](#ufs)
5. [Mesorregiões](#mesorregiões)
6. [Microrregiões](#microrregiões)
7. [Municípios](#municípios)

## Pré requisitos

* PHP >= 7.1.0
* libcurl
* composer

## Instalação

Instale pelo composer

```
$ composer require nibblelab/ibgeapiclient-php
```

## Inicialização

Inclua o composer e o namespace.

```
include './vendor/autoload.php';

use \IBGEApiClient\IBGEApiClient;

```

## Regiões

### Todas as regiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarRegioes();
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Região por ID

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarRegiaoById('1');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Regiões por IDs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarRegioesByIds(array('1','3'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

## UFs

### Todas as UF's

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarUFs(); 
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### UF por ID

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarUFById('31'); 
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### UFs por IDs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarUFsByIds(array('31','32')); 
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### UF por região

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarUFsByRegiao('3'); 
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### UF por regiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarUFsByRegioes(array('2','3')); 
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

## Mesorregiões

### Todas as Mesorregiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMesoRegioes();
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Mesorregião por ID

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMesoRegiaoById('1101');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Mesorregiões por IDs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMesoRegioesByIds(array('1101','1102'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Mesorregiões por Região

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMesoRegioesByRegiao('1');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Mesorregiões por Regiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMesoRegioesByRegioes(array('1','2'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Mesorregiões por UF

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMesoRegioesByUF('31');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Mesorregiões por UFs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMesoRegioesByUFs(array('31','27'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

## Microrregiões

### Todas as Microrregiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioes();
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregião por ID

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegiaoById('11001');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregiões por IDs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioesByIds(array('11001','11002'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregiões por Região

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioesByRegiao('1');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregiões por Regiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioesByRegioes(array('1','2'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregiões por UF

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioesByUF('31');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregiões por UFs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioesByUFs(array('31','27'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregiões por Mesorregião

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioesByMesoRegiao('1102');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Microrregiões por Mesorregiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMicroRegioesByMesoRegioes(array('1101','1102'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```


## Municípios

### Todos os Municípios

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipios();
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Município por ID

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipioById('3170206');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por IDs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByIds(array('3170206','5108352'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por Região

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByRegiao('1');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por Regiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByRegioes(array('1','2'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por UF

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByUF('31');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por UFs

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByUFs(array('31','27'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por Mesorregião

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByMesoRegiao('1102');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por Mesorregiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByMesoRegioes(array('1101','1102'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por Microrregião

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByMicroRegiao('11001');
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```

### Municípios por Microrregiões

```
try
{
    $api = new IBGEApiClient();
    $response = $api->buscarMunicipiosByMicroRegioes(array('11001','11002'));
    foreach($response->getData() as $r) {
        echo ' nome = ' . $r->getNome() . "\n"; # printe o nome
    }
} catch (Exception $ex) {
    echo $ex->getMessage();
}
```


## License

Este projeto está licenciado com Apache - veja [LICENSE.md](LICENSE.md) pra mais detalhes
