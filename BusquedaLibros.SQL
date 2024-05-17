DELIMITER ;

DROP TABLE IF EXISTS PalabrasXUsuario;
DROP TABLE IF EXISTS Palabras;
DROP TABLE IF EXISTS CaracteresXPalabraSemejante;
DROP TABLE IF EXISTS PalabrasSemejantes;
DROP TABLE IF EXISTS Usuarios;
DROP TABLE IF EXISTS PalabrasXLibro;
DROP TABLE IF EXISTS Libros;
DROP TABLE IF EXISTS PalabrasXTituloLibro;
DROP TABLE IF EXISTS TitulosLibros;
DROP TABLE IF EXISTS PalabrasXAutorLibro;
DROP TABLE IF EXISTS AutoresLibros;
DROP TABLE IF EXISTS PalabrasXTemaLibro;
DROP TABLE IF EXISTS TemasXLibro;
DROP TABLE IF EXISTS TemasLibros;

CREATE TABLE IF NOT EXISTS PalabrasSemejantes (
    IdPalabraSemejante INT NOT NULL,
    PalabraSemejante VARCHAR(100) NOT NULL,
    EstaLibre BIT NOT NULL,
    PRIMARY KEY (IdPalabraSemejante),
    CONSTRAINT IX_PalabraSemejante UNIQUE (PalabraSemejante)
);

CREATE TABLE IF NOT EXISTS CaracteresXPalabraSemejante (
    IdPalabraSemejante INT NOT NULL,
    Caracter VARCHAR(1) NOT NULL,
    PRIMARY KEY (IdPalabraSemejante, Caracter),
    FOREIGN KEY (IdPalabraSemejante) REFERENCES PalabrasSemejantes(IdPalabraSemejante)
);

CREATE TABLE IF NOT EXISTS Palabras (
    IdPalabra INT NOT NULL,
    Palabra VARCHAR(100) NOT NULL,
    EstaLibre BIT NOT NULL,
    IdPalabraSemejante INT NOT NULL,
    PRIMARY KEY (IdPalabra),
    CONSTRAINT IX_Palabra UNIQUE (Palabra),
    FOREIGN KEY (IdPalabraSemejante) REFERENCES PalabrasSemejantes(IdPalabraSemejante)
);

CREATE TABLE IF NOT EXISTS Usuarios (
    IdUsuario INT NOT NULL,
    Usuario VARCHAR(50) NOT NULL,
    Cedula VARCHAR(100) NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    Contrasena VARCHAR(50) NOT NULL,    
    EsAdministrador BIT NOT NULL,
    PRIMARY KEY (IdUsuario),
    CONSTRAINT IX_Usuario UNIQUE (Usuario)
);

CREATE TABLE IF NOT EXISTS PalabrasXUsuario (
    IdUsuario INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdUsuario, IdPalabra),
    FOREIGN KEY (IdUsuario) REFERENCES Usuarios(IdUsuario),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS TitulosLibros (
    IdTitulo INT NOT NULL,
    Titulo VARCHAR(100) NOT NULL,
    EstaLibre BIT NOT NULL,
    PRIMARY KEY (IdTitulo),
    CONSTRAINT IX_Titulo UNIQUE (Titulo)
);

CREATE TABLE IF NOT EXISTS PalabrasXTituloLibro (
    IdTitulo INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdTitulo, IdPalabra),
    FOREIGN KEY (IdTitulo) REFERENCES TitulosLibros(IdTitulo),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS AutoresLibros (
    IdAutor INT NOT NULL,
    Autor VARCHAR(100) NOT NULL,
    EstaLibre BIT NOT NULL,
    PRIMARY KEY (IdAutor),
    CONSTRAINT IX_Autor UNIQUE (Autor)
);

CREATE TABLE IF NOT EXISTS PalabrasXAutorLibro (
    IdAutor INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdAutor, IdPalabra),
    FOREIGN KEY (IdAutor) REFERENCES AutoresLibros(IdAutor),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS Libros (
    IdLibro INT NOT NULL,
    Codigo VARCHAR(50) NOT NULL,    
    IdTitulo INT NOT NULL,
    IdAutor INT NOT NULL,    
    PRIMARY KEY (IdLibro),
    CONSTRAINT IX_Codigo UNIQUE (Codigo),
    FOREIGN KEY (IdTitulo) REFERENCES TitulosLibros(IdTitulo),
    FOREIGN KEY (IdAutor) REFERENCES AutoresLibros(IdAutor)
);

CREATE TABLE IF NOT EXISTS PalabrasXLibro (
    IdLibro INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdLibro, IdPalabra),
    FOREIGN KEY (IdLibro) REFERENCES Libros(IdLibro),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS TemasLibros (
    IdTema INT NOT NULL,
    Tema VARCHAR(100) NOT NULL,
    EstaLibre BIT NOT NULL,
    PRIMARY KEY (IdTema),
    CONSTRAINT IX_Tema UNIQUE (Tema)
);

CREATE TABLE IF NOT EXISTS TemasXLibro (
    IdLibro INT NOT NULL,
    IdTema INT NOT NULL,
    PRIMARY KEY (IdLibro, IdTema),
    FOREIGN KEY (IdLibro) REFERENCES Libros(IdLibro),    
    FOREIGN KEY (IdTema) REFERENCES TemasLibros(IdTema)
);

CREATE TABLE IF NOT EXISTS PalabrasXTemaLibro (
    IdTema INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdTema, IdPalabra),
    FOREIGN KEY (IdTema) REFERENCES TemasLibros(IdTema),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

DROP PROCEDURE IF EXISTS DemeSiguientePalabra;
DROP PROCEDURE IF EXISTS DemePalabraSemejante;
DROP PROCEDURE IF EXISTS ValidarContrasena;
DROP PROCEDURE IF EXISTS Encriptar;
DROP PROCEDURE IF EXISTS SonHilerasIdenticasBitXBit;
DROP PROCEDURE IF EXISTS CambiarContrasena;
DROP PROCEDURE IF EXISTS ValidarLogin;
DROP PROCEDURE IF EXISTS IndexarPalabraSemejante;
DROP PROCEDURE IF EXISTS LiberarPalabrasSemejantesNoUtilizadas;
DROP PROCEDURE IF EXISTS IndexarPalabra;
DROP PROCEDURE IF EXISTS LiberarPalabrasNoUtilizadas;

DROP PROCEDURE IF EXISTS IndexarUsuario;
DROP PROCEDURE IF EXISTS IndexarTodosUsuarios;
DROP PROCEDURE IF EXISTS ValidarCamposUsuario;
DROP PROCEDURE IF EXISTS AltaUsuario;
DROP PROCEDURE IF EXISTS CambioUsuario;

DROP PROCEDURE IF EXISTS AltaCambioTituloLibro;
DROP PROCEDURE IF EXISTS AltaCambioAutorLibro;

DROP PROCEDURE IF EXISTS IndexarLibro;
DROP PROCEDURE IF EXISTS IndexarTituloLibro;
DROP PROCEDURE IF EXISTS IndexarAutorLibro;
DROP PROCEDURE IF EXISTS IndexarTodosLibros;
DROP PROCEDURE IF EXISTS ValidarCamposLibro;
DROP PROCEDURE IF EXISTS AltaLibro;
DROP PROCEDURE IF EXISTS CambioLibro;

DROP PROCEDURE IF EXISTS AltaCambioTemaLibro;

DROP PROCEDURE IF EXISTS IndexarTemaLibro;
DROP PROCEDURE IF EXISTS IndexarTodosTemasLibros;
DROP PROCEDURE IF EXISTS ValidarCamposTemaLibro;
DROP PROCEDURE IF EXISTS AltaTemaLibro;
DROP PROCEDURE IF EXISTS CambioTemaLibro;

DELIMITER $$

CREATE PROCEDURE DemeSiguientePalabra(IN HileraXAnalizar VARCHAR(500), IN CaracteresValidos VARCHAR(500), IN IndiceInicial INT, OUT SiguientePalabra VARCHAR(500), OUT NuevoIndice INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NuevoIndice = IndiceInicial;
  SET SiguientePalabra = '';

  WHILE NuevoIndice <= CHAR_LENGTH(HileraXAnalizar) AND POSITION(SUBSTRING(HileraXAnalizar, NuevoIndice, 1) IN CaracteresValidos) < 1 DO
    SET NuevoIndice = NuevoIndice + 1;
  END WHILE;

  WHILE NuevoIndice <= CHAR_LENGTH(HileraXAnalizar) AND POSITION(SUBSTRING(HileraXAnalizar, NuevoIndice, 1) IN CaracteresValidos) >= 1 DO
    SET SiguientePalabra = CONCAT(SiguientePalabra, SUBSTRING(HileraXAnalizar, NuevoIndice, 1));
    SET NuevoIndice = NuevoIndice + 1;
  END WHILE;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT SiguientePalabra, NuevoIndice;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE DemePalabraSemejante(IN Palabra VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), OUT PalabraSemejante VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
/* Advertencia: La hilera 'TuplasReemplazos' debe contener espacios en blanco donde se requiera, pues este procedimiento no reemplaza ninguno */

  SET PalabraSemejante = LOWER(Palabra);
  SET TuplasReemplazos = LOWER(TuplasReemplazos);
  /* Los anteriores dos reemplazos son para que no se distinga entre caracteres en minúscula y mayúscula */

  SET @Tupla = SUBSTRING_INDEX(TuplasReemplazos, SeparadorTuplas, 1);
  
  WHILE CHAR_LENGTH(@Tupla) >= 1 DO
    SET @AReemplazar = SUBSTRING_INDEX(@Tupla, SeparadorColumnas, 1);
    
    IF CHAR_LENGTH(@AReemplazar) >= 1 AND INSTR(PalabraSemejante, @AReemplazar) >= 1 THEN
      SET @Reemplazo = SUBSTRING(@Tupla, CHAR_LENGTH(@AReemplazar) + CHAR_LENGTH(SeparadorColumnas) + 1);
      SET PalabraSemejante = REPLACE(PalabraSemejante, @AReemplazar, @Reemplazo);
    END IF;

    SET TuplasReemplazos = SUBSTRING(TuplasReemplazos, CHAR_LENGTH(@Tupla) + CHAR_LENGTH(SeparadorTuplas) + 1);
    SET @Tupla = SUBSTRING_INDEX(TuplasReemplazos, SeparadorTuplas, 1);
  END WHILE;
      
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT PalabraSemejante;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE ValidarContrasena(IN Contrasena VARCHAR(500), OUT NumError INT, IN LongitudMinimaContrasena INT, IN CaracteresEspeciales VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  
  SET @CaracteresAlfabeticos = "abcdefghijklmnñopqrstuvwxyzáéíóúü";
  SET @CaracteresAlfabeticosMayuscula = UPPER(@CaracteresAlfabeticos);
  SET @CaracteresAlfabeticosMinuscula = LOWER(@CaracteresAlfabeticos);
  SET @DigitosDecimales = "0123456789";
  
  SET @ContadorCaracteresAlfabeticosMayuscula = 0;
  SET @ContadorCaracteresAlfabeticosMinuscula = 0;
  SET @ContadorDigitosDecimales = 0;
  SET @ContadorCaracteresEspeciales = 0;
  
  IF (CHAR_LENGTH(Contrasena) < LongitudMinimaContrasena) THEN
    SET NumError = 1;
  ELSE
    SET @Indice = 1;

    WHILE @Indice <= CHAR_LENGTH(Contrasena) DO
      SET @SiguienteCaracter = MID(Contrasena, @Indice, 1);
    
      IF SUBSTRING_INDEX(@CaracteresAlfabeticosMayuscula, @SiguienteCaracter, 1) != @CaracteresAlfabeticosMayuscula THEN
        SET @ContadorCaracteresAlfabeticosMayuscula = @ContadorCaracteresAlfabeticosMayuscula + 1;
      
      ELSEIF SUBSTRING_INDEX(@CaracteresAlfabeticosMinuscula, @SiguienteCaracter, 1) != @CaracteresAlfabeticosMinuscula THEN
        SET @ContadorCaracteresAlfabeticosMinuscula = @ContadorCaracteresAlfabeticosMinuscula + 1;

      ELSEIF SUBSTRING_INDEX(@DigitosDecimales, @SiguienteCaracter, 1) != @DigitosDecimales THEN
        SET @ContadorDigitosDecimales = @ContadorDigitosDecimales + 1;

      ELSEIF SUBSTRING_INDEX(CaracteresEspeciales, @SiguienteCaracter, 1) != CaracteresEspeciales THEN
        SET @ContadorCaracteresEspeciales = @ContadorCaracteresEspeciales + 1;
      
      END IF;

      SET @Indice = @Indice + 1;
    END WHILE;
    
    IF @ContadorCaracteresAlfabeticosMayuscula < 1 OR @ContadorCaracteresAlfabeticosMinuscula < 1 OR @ContadorDigitosDecimales < 1 OR @ContadorCaracteresEspeciales < 1 THEN
      SET NumError = 2;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
      SELECT NumError;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE Encriptar(IN HileraXEncriptar VARCHAR(500), IN CodigoEncriptacion VARCHAR(500), OUT HileraEncriptada VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET HileraEncriptada = '';
  
  IF CHAR_LENGTH(CodigoEncriptacion) > 0 THEN
    SET @MaximoNumeroCaracteresXCodificar = POWER(2, 7);
    SET @IndiceActual = 0;
    
    WHILE @IndiceActual < CHAR_LENGTH(HileraXEncriptar) DO
      SET @PosicionEnCodigoEncriptacion = MOD(@IndiceActual, CHAR_LENGTH(CodigoEncriptacion)) + 1;
      SET @SiguienteCaracterEnCodigoEncriptacion = MID(CodigoEncriptacion, @PosicionEnCodigoEncriptacion, 1);
      SET @SiguienteCaracterEnHileraXEncriptar = MID(HileraXEncriptar, @IndiceActual + 1, 1);
      SET @SiguienteCodigoEnHileraEncriptada = ORD(@SiguienteCaracterEnCodigoEncriptacion) + ORD(@SiguienteCaracterEnHileraXEncriptar);
      SET @SiguienteCodigoEnHileraEncriptada = MOD(@SiguienteCodigoEnHileraEncriptada, @MaximoNumeroCaracteresXCodificar);
      SET @SiguienteCaracterEnHileraEncriptada = CHAR(@SiguienteCodigoEnHileraEncriptada);
      SET HileraEncriptada = CONCAT(HileraEncriptada, @SiguienteCaracterEnHileraEncriptada);
      SET @IndiceActual = @IndiceActual + 1;
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT HileraEncriptada;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE SonHilerasIdenticasBitXBit(IN HileraComparar1 VARCHAR(500), IN HileraComparar2 VARCHAR(500), OUT SonIguales BIT(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET SonIguales = 1;
  
  IF CHAR_LENGTH(HileraComparar1) != CHAR_LENGTH(HileraComparar2) THEN
    SET SonIguales = 0;
  ELSE
    SET @IndiceActual = 1;
    
    WHILE SonIguales = 1 AND @IndiceActual <= CHAR_LENGTH(HileraComparar1) DO
      SET @SiguienteCaracterEnHilera1 = MID(HileraComparar1, @IndiceActual, 1);
      SET @SiguienteCaracterEnHilera2 = MID(HileraComparar2, @IndiceActual, 1);
      
      IF ORD(@SiguienteCaracterEnHilera1) != ORD(@SiguienteCaracterEnHilera2) THEN
          SET SonIguales = 0;
      END IF;
      
      SET @IndiceActual = @IndiceActual + 1;
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT SonIguales;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE CambiarContrasena(IN UnIdUsuario INT, IN ContrasenaAnterior VARCHAR(500), IN NuevaContrasena VARCHAR(500), IN ConfirmacionNuevaContrasena VARCHAR(500), OUT NumError INT, IN CodigoEncriptacion VARCHAR(500), IN LongitudMinimaContrasena INT, IN CaracteresEspeciales VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET NumError = 0;
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
  
  CALL Encriptar(ContrasenaAnterior, CodigoEncriptacion, @ContrasenaAnteriorEncriptada, 0);
  
  SELECT COUNT(1) FROM Usuarios WHERE IdUsuario = UnIdUsuario AND Contrasena = @ContrasenaAnteriorEncriptada INTO @CantidadUsuarios;
  
  IF @CantidadUsuarios != 1 THEN
    SET NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    
    CALL SonHilerasIdenticasBitXBit(NuevaContrasena, ConfirmacionNuevaContrasena, @SonIguales, 0);
    
    IF @SonIguales = 0 THEN
      SET NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      
      CALL ValidarContrasena(NuevaContrasena, NumError, LongitudMinimaContrasena, CaracteresEspeciales, 0);
      
      IF NumError != 0 THEN
        SET NumError = @NumBaseError + NumError;
      END IF;
    END IF;
  END IF;
  
  IF NumError = 0 THEN
    CALL Encriptar(NuevaContrasena, CodigoEncriptacion, @NuevaContrasenaEncriptada, 0);
    UPDATE Usuarios SET Contrasena = @NuevaContrasenaEncriptada WHERE IdUsuario = UnIdUsuario;
  END IF;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;

  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE ValidarLogin(IN UnUsuario VARCHAR(500), IN UnaContrasena VARCHAR(500), IN CodigoEncriptacion VARCHAR(500), OUT UsuarioContrasenaExiste BIT(1), OUT UnIdUsuario INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET UsuarioContrasenaExiste = 0;
  CALL Encriptar(UnaContrasena, CodigoEncriptacion, @UnaContrasenaEncriptada, 0);
  
  SELECT COUNT(1), MIN(IdUsuario) FROM Usuarios WHERE Usuario = UnUsuario AND Contrasena = @UnaContrasenaEncriptada INTO @CantidadUsuarios, UnIdUsuario;
  
  IF @CantidadUsuarios = 1 THEN
    SET UsuarioContrasenaExiste = 1;
  END IF;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT UsuarioContrasenaExiste, UnIdUsuario;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE IndexarPalabraSemejante(IN UnaPalabraSemejante VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  SET @ExistePalabraSemejante = 1;
  SELECT MIN(IdPalabraSemejante) FROM PalabrasSemejantes WHERE PalabraSemejante = UnaPalabraSemejante INTO @IdPalabraSemejante;
  
  IF ISNULL(@IdPalabraSemejante) = 1 THEN
    SELECT MIN(IdPalabraSemejante) FROM PalabrasSemejantes WHERE EstaLibre = 1 INTO @IdPalabraSemejante;
  END IF;
  
  IF ISNULL(@IdPalabraSemejante) = 1 THEN
    SET @ExistePalabraSemejante = 0;
    SELECT MAX(IdPalabraSemejante) FROM PalabrasSemejantes INTO @IdPalabraSemejante;
      
    IF ISNULL(@IdPalabraSemejante) = 1 THEN
      SET @IdPalabraSemejante = 0;
    END IF;
    
    SET @IdPalabraSemejante = @IdPalabraSemejante + 1;
  END IF;
  
  IF @ExistePalabraSemejante = 0 THEN
    INSERT INTO PalabrasSemejantes(IdPalabraSemejante, PalabraSemejante, EstaLibre) VALUES(@IdPalabraSemejante, UnaPalabraSemejante, 0);
  ELSE
    UPDATE PalabrasSemejantes SET PalabraSemejante = UnaPalabraSemejante, EstaLibre = 0 WHERE IdPalabraSemejante = @IdPalabraSemejante;
  END IF;
    
  DELETE FROM CaracteresXPalabraSemejante WHERE IdPalabraSemejante = @IdPalabraSemejante;

  IF CHAR_LENGTH(UnaPalabraSemejante) = 0 THEN
    INSERT INTO CaracteresXPalabraSemejante(IdPalabraSemejante, Caracter) VALUES(@IdPalabraSemejante, '');
  ELSE
    SET @IndiceActual = 1;
    SET @CaracteresInsertados = "";
      
    WHILE @IndiceActual <= CHAR_LENGTH(UnaPalabraSemejante) DO
      SET @SiguienteCaracter = MID(UnaPalabraSemejante, @IndiceActual, 1);
        
      IF INSTR(@CaracteresInsertados, @SiguienteCaracter) < 1 THEN
        INSERT INTO CaracteresXPalabraSemejante(IdPalabraSemejante, Caracter) VALUES(@IdPalabraSemejante, @SiguienteCaracter);
        SET @CaracteresInsertados = CONCAT(@CaracteresInsertados, @SiguienteCaracter);
      END IF;
        
      SET @IndiceActual = @IndiceActual + 1;
    END WHILE;
  END IF;
      
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdPalabraSemejante, a.PalabraSemejante, a.EstaLibre, b.Caracter
    FROM PalabrasSemejantes a, CaracteresXPalabraSemejante b
    WHERE a.IdPalabraSemejante = b.IdPalabraSemejante
    AND a.PalabraSemejante = UnaPalabraSemejante;
  END IF;
  
  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE LiberarPalabrasSemejantesNoUtilizadas()
BEGIN
  START TRANSACTION;
  
  UPDATE PalabrasSemejantes
  SET EstaLibre = 1
  WHERE NOT IdPalabraSemejante IN (SELECT IdPalabraSemejante FROM Palabras WHERE EstaLibre = 0);
  
  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE IndexarPalabra(IN UnaPalabra VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  SET @ExistePalabra = 1;
  SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = UnaPalabra INTO @IdPalabra;
  
  IF ISNULL(@IdPalabra) = 1 THEN
    SELECT MIN(IdPalabra) FROM Palabras WHERE EstaLibre = 1 INTO @IdPalabra;
  END IF;
  
  IF ISNULL(@IdPalabra) = 1 THEN
    SET @ExistePalabra = 0;
    SELECT MAX(IdPalabra) FROM Palabras INTO @IdPalabra;
      
    IF ISNULL(@IdPalabra) = 1 THEN
      SET @IdPalabra = 0;
    END IF;
    
    SET @IdPalabra = @IdPalabra + 1;
  END IF;

  CALL DemePalabraSemejante(UnaPalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, @PalabraSemejante, 0);
  CALL IndexarPalabraSemejante(@PalabraSemejante, 0);

  SELECT MIN(IdPalabraSemejante) FROM PalabrasSemejantes WHERE PalabraSemejante = @PalabraSemejante INTO @IdPalabraSemejante;

  IF @ExistePalabra = 0 THEN
    INSERT INTO Palabras(IdPalabra, Palabra, EstaLibre, IdPalabraSemejante) VALUES(@IdPalabra, UnaPalabra, 0, @IdPalabraSemejante);
  ELSE
    UPDATE Palabras SET Palabra = UnaPalabra, EstaLibre = 0, IdPalabraSemejante = @IdPalabraSemejante WHERE IdPalabra = @IdPalabra;
  END IF;
  
  CALL LiberarPalabrasSemejantesNoUtilizadas();

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdPalabra, a.Palabra, a.EstaLibre, b.IdPalabraSemejante, b.PalabraSemejante, b.EstaLibre, c.Caracter
    FROM Palabras a, PalabrasSemejantes b, CaracteresXPalabraSemejante c
    WHERE a.IdPalabraSemejante = b.IdPalabraSemejante
    AND b.IdPalabraSemejante = c.IdPalabraSemejante
    AND a.Palabra = UnaPalabra;
  END IF;
  
  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE LiberarPalabrasNoUtilizadas()
BEGIN
  START TRANSACTION;
  
  UPDATE Palabras
  SET EstaLibre = 1
  WHERE NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXUsuario)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXLibro)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXTituloLibro)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXAutorLibro)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXTemaLibro);
        
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarUsuario(IN UnIdUsuario INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXUsuario WHERE IdUsuario = UnIdUsuario;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(CONCAT(Usuario, SeparadorPalabras, Cedula, SeparadorPalabras, Nombre)) FROM Usuarios WHERE IdUsuario = UnIdUsuario INTO @CantidadUsuarios, @PalabrasXIndexar;
  
  IF (@CantidadUsuarios = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXUsuario(IdUsuario, IdPalabra) VALUES(UnIdUsuario, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdUsuario, a.Usuario, a.Cedula, a.Nombre, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Usuarios a, PalabrasXUsuario b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdUsuario = b.IdUsuario
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdUsuario = UnIdUsuario;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodosUsuarios(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE UsuariosXIndexar
  SELECT IdUsuario
  FROM Usuarios
  ORDER BY IdUsuario ASC;

  SELECT MIN(IdUsuario) FROM UsuariosXIndexar INTO @IdUsuario;
  
  WHILE ISNULL(@IdUsuario) = 0 DO
    CALL IndexarUsuario(@IdUsuario, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdUsuario) FROM UsuariosXIndexar WHERE IdUsuario > @IdUsuario INTO @IdUsuario;
  END WHILE;
  
  DROP TEMPORARY TABLE UsuariosXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdUsuario, a.Usuario, a.Cedula, a.Nombre, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Usuarios a, PalabrasXUsuario b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdUsuario = b.IdUsuario
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposUsuario(IN UnUsuario VARCHAR(50), IN UnaCedula VARCHAR(100), IN UnNombre VARCHAR(100), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnUsuario, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1;
  ELSE
    CALL DemeSiguientePalabra(UnaCedula, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

    IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
      SET NumError = 2;
    ELSE
      CALL DemeSiguientePalabra(UnNombre, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

      IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
        SET NumError = 3;
      END IF;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaUsuario(IN UnUsuario VARCHAR(50), IN UnaCedula VARCHAR(100), IN UnNombre VARCHAR(100), BitEsAdministrador BIT(1), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdUsuario) FROM Usuarios WHERE Usuario = UnUsuario INTO @IdUsuario;
  
  IF ISNULL(@IdUsuario) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposUsuario(UnUsuario, UnaCedula, UnNombre, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      SELECT MAX(IdUsuario) FROM Usuarios INTO @IdUsuario;
  
      IF ISNULL(@IdUsuario) = 1 THEN
        SET @IdUsuario = 0;
      END IF;
    
      SET @IdUsuario = @IdUsuario + 1;

      INSERT INTO Usuarios(IdUsuario, Usuario, Cedula, Nombre, Contrasena, EsAdministrador) VALUES(@IdUsuario, UnUsuario, UnaCedula, UnNombre, '', BitEsAdministrador);
      CALL IndexarUsuario(@IdUsuario, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdUsuario;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioUsuario(IN UnIdUsuario INT, IN UnUsuario VARCHAR(50), IN UnaCedula VARCHAR(100), IN UnNombre VARCHAR(100), IN BitEsAdministrador BIT(1), BorrarContrasena BIT(1), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
   
  SET @NumError = 0;
  SELECT MIN(IdUsuario) FROM Usuarios WHERE IdUsuario <> UnIdUsuario AND Usuario = UnUsuario INTO @IdUsuario;
  
  IF ISNULL(@IdUsuario) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdUsuario) FROM Usuarios WHERE IdUsuario = UnIdUsuario INTO @IdUsuario;
    
    IF ISNULL(@IdUsuario) = 1 THEN
      SET @NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposUsuario(UnUsuario, UnaCedula, UnNombre, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        UPDATE Usuarios SET Usuario = UnUsuario, Cedula = UnaCedula, Nombre = UnNombre, EsAdministrador = BitEsAdministrador WHERE IdUsuario = @IdUsuario;
      
        IF BorrarContrasena = 1 THEN
          UPDATE Usuarios SET Contrasena = '' WHERE IdUsuario = @IdUsuario;
        END IF;
      
        CALL IndexarUsuario(@IdUsuario, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;    
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE AltaCambioTituloLibro(IN UnIdTitulo INT, IN UnTitulo VARCHAR(100), OUT UnNuevoIdTitulo INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  /*
  Prioridad de asignación de Id:
  1. Que coincida el título en la tabla de títulos.
  2. Que coincida el id si el registro está libre en la tabla de títulos.
  3. Que haya un registro libre en la tabla de títulos. (Se toma ese id).
  4. Si no se cumple ninguna de las anteriores condiciones, se inserta un nuevo registro en la tabla de títulos.
  */
  
  START TRANSACTION;
  
  IF ISNULL(UnIdTitulo) = 0 THEN
    SELECT MIN(IdTitulo) FROM TitulosLibros WHERE IdTitulo = UnIdTitulo AND EstaLibre = 1 INTO @IdTituloConsulta;
    /* Actualiza el dato con el id recibido como parámetro, solo si está libre, pues lo puede estar empleando otro registro en otra tabla */
    
    IF ISNULL(@IdTituloConsulta) = 1 THEN
      SET UnIdTitulo = NULL;
    ELSE
      SET UnIdTitulo = @IdTituloConsulta;
    END IF;
  END IF;
  
  SELECT MIN(IdTitulo) FROM TitulosLibros WHERE Titulo = UnTitulo INTO @IdTituloConsulta;

  IF ISNULL(@IdTituloConsulta) = 1 THEN
    IF ISNULL(UnIdTitulo) = 0 THEN
      SET @IdTituloConsulta = UnIdTitulo;
      /* Le da prioridad al dato encontrado en UnIdTitulo */
    ELSE
      SELECT MIN(IdTitulo) FROM TitulosLibros WHERE EstaLibre = 1 INTO @IdTituloConsulta;
    END IF;
  END IF;
  
  IF ISNULL(@IdTituloConsulta) = 1 THEN
    SET UnNuevoIdTitulo = NULL;
  ELSE
    SET UnNuevoIdTitulo = @IdTituloConsulta;
  END IF;
  
  IF ISNULL(UnIdTitulo) = 0 AND ISNULL(UnNuevoIdTitulo) = 1 THEN
    SET UnNuevoIdTitulo = UnIdTitulo;
  END IF;
  
  IF ISNULL(UnIdTitulo) = 1 AND ISNULL(UnNuevoIdTitulo) = 0 THEN
    SET UnIdTitulo = UnNuevoIdTitulo;
  END IF;

  IF ISNULL(UnIdTitulo) = 1 AND ISNULL(UnNuevoIdTitulo) = 1 THEN
    SELECT MAX(IdTitulo) FROM TitulosLibros INTO @IdTitulo;

    IF ISNULL(@IdTitulo) = 1 THEN
      SET @IdTitulo = 0;
    END IF;

    SET @IdTitulo = @IdTitulo + 1;
    INSERT INTO TitulosLibros(IdTitulo, Titulo, EstaLibre) VALUES(@IdTitulo, UnTitulo, 1);
    SET UnIdTitulo = @IdTitulo;
    SET UnNuevoIdTitulo = @IdTitulo;
  END IF;

  IF ISNULL(UnIdTitulo) = 0 AND ISNULL(UnNuevoIdTitulo) = 0 THEN
    UPDATE TitulosLibros SET Titulo = UnTitulo WHERE IdTitulo = UnNuevoIdTitulo;
  END IF;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT IdTitulo, Titulo, EstaLibre, 'Nuevo Id' as Tipo
    FROM TitulosLibros
    WHERE IdTitulo = UnNuevoIdTitulo
    UNION
    SELECT IdTitulo, Titulo, EstaLibre, 'Id Anterior' as Tipo
    FROM TitulosLibros
    WHERE IdTitulo = UnIdTitulo
    UNION
    SELECT IdTitulo, Titulo, EstaLibre, 'Otros Ids' as Tipo
    FROM TitulosLibros
    WHERE IdTitulo != UnNuevoIdTitulo
    AND IdTitulo != UnIdTitulo;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE AltaCambioAutorLibro(IN UnIdAutor INT, IN UnAutor VARCHAR(100), OUT UnNuevoIdAutor INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  /*
  Prioridad de asignación de Id:
  1. Que coincida el autor en la tabla de autores.
  2. Que coincida el id si el registro está libre en la tabla de autores.
  3. Que haya un registro libre en la tabla de autores. (Se toma ese id).
  4. Si no se cumple ninguna de las anteriores condiciones, se inserta un nuevo registro en la tabla de autores.
  */
  
  START TRANSACTION;
  
  IF ISNULL(UnIdAutor) = 0 THEN
    SELECT MIN(IdAutor) FROM AutoresLibros WHERE IdAutor = UnIdAutor AND EstaLibre = 1 INTO @IdAutorConsulta;
    /* Actualiza el dato con el id recibido como parámetro, solo si está libre, pues lo puede estar empleando otro registro en otra tabla */
    
    IF ISNULL(@IdAutorConsulta) = 1 THEN
      SET UnIdAutor = NULL;
    ELSE
      SET UnIdAutor = @IdAutorConsulta;
    END IF;
  END IF;
  
  SELECT MIN(IdAutor) FROM AutoresLibros WHERE Autor = UnAutor INTO @IdAutorConsulta;

  IF ISNULL(@IdAutorConsulta) = 1 THEN
    IF ISNULL(UnIdAutor) = 0 THEN
      SET @IdAutorConsulta = UnIdAutor;
      /* Le da prioridad al dato encontrado en UnIdAutor */
    ELSE
      SELECT MIN(IdAutor) FROM AutoresLibros WHERE EstaLibre = 1 INTO @IdAutorConsulta;
    END IF;
  END IF;
  
  IF ISNULL(@IdAutorConsulta) = 1 THEN
    SET UnNuevoIdAutor = NULL;
  ELSE
    SET UnNuevoIdAutor = @IdAutorConsulta;
  END IF;
  
  IF ISNULL(UnIdAutor) = 0 AND ISNULL(UnNuevoIdAutor) = 1 THEN
    SET UnNuevoIdAutor = UnIdAutor;
  END IF;
  
  IF ISNULL(UnIdAutor) = 1 AND ISNULL(UnNuevoIdAutor) = 0 THEN
    SET UnIdAutor = UnNuevoIdAutor;
  END IF;

  IF ISNULL(UnIdAutor) = 1 AND ISNULL(UnNuevoIdAutor) = 1 THEN
    SELECT MAX(IdAutor) FROM AutoresLibros INTO @IdAutor;

    IF ISNULL(@IdAutor) = 1 THEN
      SET @IdAutor = 0;
    END IF;

    SET @IdAutor = @IdAutor + 1;
    INSERT INTO AutoresLibros(IdAutor, Autor, EstaLibre) VALUES(@IdAutor, UnAutor, 1);
    SET UnIdAutor = @IdAutor;
    SET UnNuevoIdAutor = @IdAutor;
  END IF;

  IF ISNULL(UnIdAutor) = 0 AND ISNULL(UnNuevoIdAutor) = 0 THEN
    UPDATE AutoresLibros SET Autor = UnAutor WHERE IdAutor = UnNuevoIdAutor;
  END IF;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT IdAutor, Autor, EstaLibre, 'Nuevo Id' as Tipo
    FROM AutoresLibros
    WHERE IdAutor = UnNuevoIdAutor
    UNION
    SELECT IdAutor, Autor, EstaLibre, 'Id Anterior' as Tipo
    FROM AutoresLibros
    WHERE IdAutor = UnIdAutor
    UNION
    SELECT IdAutor, Autor, EstaLibre, 'Otros Ids' as Tipo
    FROM AutoresLibros
    WHERE IdAutor != UnNuevoIdAutor
    AND IdAutor != UnIdAutor;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarLibro(IN UnIdLibro INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXLibro WHERE IdLibro = UnIdLibro;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(Codigo) FROM Libros WHERE IdLibro = UnIdLibro INTO @CantidadLibros, @PalabrasXIndexar;
  
  IF (@CantidadLibros = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXLibro(IdLibro, IdPalabra) VALUES(UnIdLibro, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdLibro, a.Codigo, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Libros a, PalabrasXLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdLibro = b.IdLibro
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdLibro = UnIdLibro;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTituloLibro(IN UnIdLibro INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  SELECT COUNT(1), MIN(IdTitulo) from Libros WHERE IdLibro = UnIdLibro INTO @CantidadTitulos, @IdTitulo;
  
  IF (@CantidadTitulos = 1) THEN
    DELETE FROM PalabrasXTituloLibro WHERE IdTitulo = @IdTitulo;
    CALL LiberarPalabrasNoUtilizadas();
    
    SELECT COUNT(1), LOWER(Titulo) FROM TitulosLibros WHERE IdTitulo = @IdTitulo INTO @CantidadTitulos, @PalabrasXIndexar;    
  END IF;
    
  IF (@CantidadTitulos = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXTituloLibro(IdTitulo, IdPalabra) VALUES(@IdTitulo, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a1.IdLibro, a2.IdTitulo, a2.Titulo, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Libros a1, TitulosLibros a2, PalabrasXTituloLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a1.IdTitulo = a2.IdTitulo
    AND a2.IdTitulo = b.IdTitulo
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a1.IdLibro = UnIdLibro;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarAutorLibro(IN UnIdLibro INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  SELECT COUNT(1), MIN(IdAutor) from Libros WHERE IdLibro = UnIdLibro INTO @CantidadAutores, @IdAutor;
  
  IF (@CantidadAutores = 1) THEN
    DELETE FROM PalabrasXAutorLibro WHERE IdAutor = @IdAutor;
    CALL LiberarPalabrasNoUtilizadas();
    
    SELECT COUNT(1), LOWER(Autor) FROM AutoresLibros WHERE IdAutor = @IdAutor INTO @CantidadAutores, @PalabrasXIndexar;    
  END IF;
    
  IF (@CantidadAutores = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXAutorLibro(IdAutor, IdPalabra) VALUES(@IdAutor, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a1.IdLibro, a2.IdAutor, a2.Autor, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Libros a1, AutoresLibros a2, PalabrasXAutorLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a1.IdAutor = a2.IdAutor
    AND a2.IdAutor = b.IdAutor
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a1.IdLibro = UnIdLibro;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodosLibros(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE LibrosXIndexar
  SELECT IdLibro
  FROM Libros
  ORDER BY IdLibro ASC;

  SELECT MIN(IdLibro) FROM LibrosXIndexar INTO @IdLibro;
  
  WHILE ISNULL(@IdLibro) = 0 DO
    CALL IndexarLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    CALL IndexarTituloLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);    
    CALL IndexarAutorLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdLibro) FROM LibrosXIndexar WHERE IdLibro > @IdLibro INTO @IdLibro;
  END WHILE;
  
  DROP TEMPORARY TABLE LibrosXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdLibro, a.IdLibro, a.Codigo, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Libros a, PalabrasXLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdLibro = b.IdLibro
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    UNION
    SELECT a1.IdLibro, a2.IdTitulo, a2.Titulo, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Libros a1, TitulosLibros a2, PalabrasXTituloLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a1.IdTitulo = a2.IdTitulo
    AND a2.IdTitulo = b.IdTitulo
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    UNION
    SELECT a1.IdLibro, a2.IdAutor, a2.Autor, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Libros a1, AutoresLibros a2, PalabrasXAutorLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a1.IdAutor = a2.IdAutor
    AND a2.IdAutor = b.IdAutor
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposLibro(IN UnCodigo VARCHAR(50), IN UnTitulo VARCHAR(100), IN UnAutor VARCHAR(100), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnCodigo, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1;
  ELSE
    CALL DemeSiguientePalabra(UnTitulo, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

    IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
      SET NumError = 2;
    ELSE
      CALL DemeSiguientePalabra(UnAutor, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

      IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
        SET NumError = 3;
      END IF;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaLibro(IN UnCodigo VARCHAR(50), IN UnTitulo VARCHAR(100), IN UnAutor VARCHAR(100), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdLibro) FROM Libros WHERE Codigo = UnCodigo INTO @IdLibro;
  
  IF ISNULL(@IdLibro) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposLibro(UnCodigo, UnTitulo, UnAutor, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      CALL AltaCambioTituloLibro(NULL, UnTitulo, @IdTitulo, 0);
      CALL AltaCambioAutorLibro(NULL, UnAutor, @IdAutor, 0);
      
      SELECT MAX(IdLibro) FROM Libros INTO @IdLibro;
  
      IF ISNULL(@IdLibro) = 1 THEN
        SET @IdLibro = 0;
      END IF;
    
      SET @IdLibro = @IdLibro + 1;

      INSERT INTO Libros(IdLibro, Codigo, IdTitulo, IdAutor) VALUES(@IdLibro, UnCodigo, @IdTitulo, @IdAutor);
      UPDATE TitulosLibros SET EstaLibre = 0 where IdTitulo = @IdTitulo;
      UPDATE AutoresLibros SET EstaLibre = 0 where IdAutor = @IdAutor;
      CALL IndexarLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      CALL IndexarTituloLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);      
      CALL IndexarAutorLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdLibro;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioLibro(IN UnIdLibro INT, IN UnCodigo VARCHAR(50), IN UnTitulo VARCHAR(100), IN UnAutor VARCHAR(100), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
   
  SET @NumError = 0;
  SELECT MIN(IdLibro) FROM Libros WHERE IdLibro <> UnIdLibro AND Codigo = UnCodigo INTO @IdLibro;
  
  IF ISNULL(@IdLibro) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdLibro) FROM Libros WHERE IdLibro = UnIdLibro INTO @IdLibro;
    
    IF ISNULL(@IdLibro) = 1 THEN
      SET @NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposLibro(UnCodigo, UnTitulo, UnAutor, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        SELECT IdTitulo, IdAutor FROM Libros WHERE IdLibro = @IdLibro INTO @IdTitulo, @IdAutor;
        
        UPDATE TitulosLibros SET EstaLibre = 1 where IdTitulo = @IdTitulo AND NOT IdTitulo IN (SELECT IdTitulo FROM Libros WHERE IdLibro <> @IdLibro);
        UPDATE AutoresLibros SET EstaLibre = 1 where IdAutor = @IdAutor AND NOT IdAutor IN (SELECT IdAutor FROM Libros WHERE IdLibro <> @IdLibro);
        
        CALL AltaCambioTituloLibro(@IdTitulo, UnTitulo, @IdNuevoTitulo, 0);
        CALL AltaCambioAutorLibro(@IdAutor, UnAutor, @IdNuevoAutor, 0);
        
        UPDATE Libros SET Codigo = UnCodigo, IdTitulo = @IdNuevoTitulo, IdAutor = @IdNuevoAutor WHERE IdLibro = @IdLibro;
        
        UPDATE TitulosLibros SET EstaLibre = 0 where IdTitulo = @IdNuevoTitulo;
        UPDATE AutoresLibros SET EstaLibre = 0 where IdAutor = @IdNuevoAutor;
        
        DELETE FROM PalabrasXTituloLibro where IdTitulo IN (SELECT IdTitulo FROM TitulosLibros WHERE IdTitulo = @IdTitulo AND EstaLibre = 1);
        DELETE FROM PalabrasXAutorLibro where IdAutor IN (SELECT IdAutor FROM AutoresLibros WHERE IdAutor = @IdAutor AND EstaLibre = 1);        
        
        CALL IndexarLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
        CALL IndexarTituloLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);      
        CALL IndexarAutorLibro(@IdLibro, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;    
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE AltaCambioTemaLibro(IN UnIdTema INT, IN UnTema VARCHAR(100), OUT UnNuevoIdTema INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  /*
  Prioridad de asignación de Id:
  1. Que coincida el tema en la tabla de temas.
  2. Que coincida el id si el registro está libre en la tabla de temas.
  3. Que haya un registro libre en la tabla de temas. (Se toma ese id).
  4. Si no se cumple ninguna de las anteriores condiciones, se inserta un nuevo registro en la tabla de temas.
  */
  
  START TRANSACTION;
  
  IF ISNULL(UnIdTema) = 0 THEN
    SELECT MIN(IdTema) FROM TemasLibros WHERE IdTema = UnIdTema AND EstaLibre = 1 INTO @IdTemaConsulta;
    /* Actualiza el dato con el id recibido como parámetro, solo si está libre, pues lo puede estar empleando otro registro en otra tabla */
    
    IF ISNULL(@IdTemaConsulta) = 1 THEN
      SET UnIdTema = NULL;
    ELSE
      SET UnIdTema = @IdTemaConsulta;
    END IF;
  END IF;
  
  SELECT MIN(IdTema) FROM TemasLibros WHERE Tema = UnTema INTO @IdTemaConsulta;

  IF ISNULL(@IdTemaConsulta) = 1 THEN
    IF ISNULL(UnIdTema) = 0 THEN
      SET @IdTemaConsulta = UnIdTema;
      /* Le da prioridad al dato encontrado en UnIdTema */
    ELSE
      SELECT MIN(IdTema) FROM TemasLibros WHERE EstaLibre = 1 INTO @IdTemaConsulta;
    END IF;
  END IF;
  
  IF ISNULL(@IdTemaConsulta) = 1 THEN
    SET UnNuevoIdTema = NULL;
  ELSE
    SET UnNuevoIdTema = @IdTemaConsulta;
  END IF;
  
  IF ISNULL(UnIdTema) = 0 AND ISNULL(UnNuevoIdTema) = 1 THEN
    SET UnNuevoIdTema = UnIdTema;
  END IF;
  
  IF ISNULL(UnIdTema) = 1 AND ISNULL(UnNuevoIdTema) = 0 THEN
    SET UnIdTema = UnNuevoIdTema;
  END IF;

  IF ISNULL(UnIdTema) = 1 AND ISNULL(UnNuevoIdTema) = 1 THEN
    SELECT MAX(IdTema) FROM TemasLibros INTO @IdTema;

    IF ISNULL(@IdTema) = 1 THEN
      SET @IdTema = 0;
    END IF;

    SET @IdTema = @IdTema + 1;
    INSERT INTO TemasLibros(IdTema, Tema, EstaLibre) VALUES(@IdTema, UnTema, 1);
    SET UnIdTema = @IdTema;
    SET UnNuevoIdTema = @IdTema;
  END IF;

  IF ISNULL(UnIdTema) = 0 AND ISNULL(UnNuevoIdTema) = 0 THEN
    UPDATE TemasLibros SET Tema = UnTema WHERE IdTema = UnNuevoIdTema;
  END IF;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT IdTema, Tema, EstaLibre, 'Nuevo Id' as Tipo
    FROM TemasLibros
    WHERE IdTema = UnNuevoIdTema
    UNION
    SELECT IdTema, Tema, EstaLibre, 'Id Anterior' as Tipo
    FROM TemasLibros
    WHERE IdTema = UnIdTema
    UNION
    SELECT IdTema, Tema, EstaLibre, 'Otros Ids' as Tipo
    FROM TemasLibros
    WHERE IdTema != UnNuevoIdTema
    AND IdTema != UnIdTema;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTemaLibro(IN UnIdTema INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXTemaLibro WHERE IdTema = UnIdTema;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(Tema) FROM TemasLibros WHERE IdTema = UnIdTema INTO @CantidadTemas, @PalabrasXIndexar;
  
  IF (@CantidadTemas = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXTemaLibro(IdTema, IdPalabra) VALUES(UnIdTema, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdTema, a.Tema, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM TemasLibros a, PalabrasXTemaLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdTema = b.IdTema
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdTema = UnIdTema;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodosTemasLibros(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE TemasLibrosXIndexar
  SELECT DISTINCT IdTema
  FROM TemasXLibro
  ORDER BY IdTema ASC;

  SELECT MIN(IdTema) FROM TemasLibrosXIndexar INTO @IdTema;
  
  WHILE ISNULL(@IdTema) = 0 DO
    CALL IndexarTemaLibro(@IdTema, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdTema) FROM TemasLibrosXIndexar WHERE IdTema > @IdTema INTO @IdTema;
  END WHILE;
  
  DROP TEMPORARY TABLE TemasLibrosXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a1.IdLibro, a1.IdTema, a2.Tema, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM TemasXLibro a1, TemasLibros a2, PalabrasXTemaLibro b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a1.IdTema = a2.IdTema
    AND a2.IdTema = b.IdTema
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposTemaLibro(IN UnTema VARCHAR(100), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnTema, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaTemaLibro(IN UnIdLibro INT, IN UnTema VARCHAR(100), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdLibro) FROM Libros WHERE IdLibro = UnIdLibro INTO @IdLibroConsulta;
  
  IF ISNULL(@IdLibroConsulta) = 1 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposTemaLibro(UnTema, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      CALL AltaCambioTemaLibro(NULL, UnTema, @IdTema, 0);

      SELECT MIN(IdTema) FROM TemasXLibro WHERE IdLibro = UnIdLibro AND IdTema = @IdTema INTO @IdTemaConsulta;
      
      IF ISNULL(@IdTemaConsulta) = 1 THEN
        INSERT INTO TemasXLibro(IdLibro, IdTema) VALUES(UnIdLibro, @IdTema);
      END IF;
      
      UPDATE TemasLibros SET EstaLibre = 0 where IdTema = @IdTema;
      
      CALL IndexarTemaLibro(@IdTema, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdTema;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioTemaLibro(IN UnIdLibro INT, IN UnIdTema INT, IN UnTema VARCHAR(100), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdLibro) FROM Libros WHERE IdLibro = UnIdLibro INTO @IdLibroConsulta;
  
  IF ISNULL(@IdLibroConsulta) = 1 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposTemaLibro(UnTema, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      UPDATE TemasLibros SET EstaLibre = 1 where IdTema = UnIdTema AND NOT IdTema IN (SELECT IdTema FROM TemasXLibro WHERE IdLibro <> UnIdLibro);
            
      CALL AltaCambioTemaLibro(UnIdTema, UnTema, @IdNuevoTema, 0);

      IF ISNULL(UnIdTema) = 0 AND ISNULL(@IdNuevoTema) = 0 AND UnIdTema != @IdNuevoTema THEN
        DELETE FROM TemasXLibro WHERE IdLibro = UnIdLibro AND IdTema = UnIdTema;
      END IF;
        
      SELECT MIN(IdTema) FROM TemasXLibro WHERE IdLibro = UnIdLibro AND IdTema = @IdNuevoTema INTO @IdNuevoTemaConsulta;
      
      IF ISNULL(@IdNuevoTemaConsulta) = 1 THEN
        INSERT INTO TemasXLibro(IdLibro, IdTema) VALUES(UnIdLibro, @IdNuevoTema);
      END IF;
       
      UPDATE TemasLibros SET EstaLibre = 0 where IdTema = @IdNuevoTema;
        
      DELETE FROM PalabrasXTemaLibro where IdTema IN (SELECT IdTema FROM TemasLibros WHERE IdTema = UnIdTema AND EstaLibre = 1);
        
      CALL IndexarTemaLibro(@IdNuevoTema, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdNuevoTema;
  END IF;
  
  COMMIT; 
END;
