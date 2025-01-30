# VyVBTSPractica1
1.  Primer caso de uso: CONSULTAR INFORMACIÓN DE UN STREAMER DE TWITCH
    Comando para hacer la petición: curl -X GET "s1037905437.mialojamiento.es/user?id=XXX"
    En el navegador: s1037905437.mialojamiento.es/user?id=XXX
    Siendo XXX cualquier id valido, como puede ser el id=1 mismamente.
    Si no pones la variable id --> Error 400
    Si la dejas vacia(user?id=) --> Error 400
    Si pones un id que no existe como 1900000009 --> Error 404
    Token expirado --> Error 401
    Internal server error --> 500
    
2.  Segundo caso de uso:  CONSULTAR STREAMS EN VIVO
    Comando para hacer la petición: curl -X GET "s1037905437.mialojamiento.es/streams"
    En el navegador: s1037905437.mialojamiento.es/streams
    Token expirado --> Error 401
    Internal server error --> 500
    
3.  Tercer caso de uso: CONSULTAR “TOP STREAMS ENRIQUECIDOS”
    Comando para hacer la petición: curl -X GET "s1037905437.mialojamiento.es/enriched?limit=XXX"
    En el navegador: s1037905437.mialojamiento.es/enriched?limit=XXX
    Siendo XXX cualquier limite entre 1 y 100.
    Si no pones la variable limit --> Error 400
    Si la dejas vacia(enriched?limit=) --> Error 400
    Token expirado --> Error 401
    Internal server error --> 500
