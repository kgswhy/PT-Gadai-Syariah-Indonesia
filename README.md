
---

## Authentication & Authorization

### Authentication Method
This API uses **JWT (JSON Web Token)** for authentication. Every request that requires authentication must include the token in the `Authorization` header.

### Endpoints

#### **POST /api/register**
- **Description**: Register a new user.
- **Request Body**:
    ```json
    {
      "username": "string",
      "email": "string",
      "password": "string",
    }
    ```
- **Response**:
    - **200 OK**:
    ```json
    {
      "status": true,
      "message": "User created successfully",
      "token": "JWT_Token_String"
    }
    ```
    - **422 Unprocessable Entity**: If validation errors occur.
    - **500 Internal Server Error**: If an error occurs during registration.

---

#### **POST /api/login**
- **Description**: Login a user and obtain a JWT token.
- **Request Body**:
    ```json
    {
      "username": "string",
      "password": "string"
    }
    ```
- **Response**:
    - **200 OK**:
    ```json
    {
      "status": true,
      "message": "Login successful",
      "token": "JWT_Token_String"
    }
    ```
    - **401 Unauthorized**: If invalid credentials are provided.
    - **422 Unprocessable Entity**: If validation errors occur.
    - **500 Internal Server Error**: If an error occurs during login.

---

#### **POST /api/logout**
- **Description**: Logs out the user and invalidates the JWT token.
- **Authentication**: Requires a valid JWT token.
- **Response**:
    - **200 OK**:
    ```json
    {
      "status": true,
      "message": "Successfully logged out"
    }
    ```
    - **500 Internal Server Error**: If an error occurs during logout.

---

## Protected Routes (Requires JWT Authentication)

These routes are protected and require a valid JWT token in the `Authorization` header.

#### **GET /api/get-profile**
- **Description**: Retrieve the profile information of the currently authenticated user.
- **Authentication**: Requires a valid JWT token.
- **Response**:
    - **200 OK**:
    ```json
    {
      "status": true,
      "message": "Profile info retrieved successfully.",
      "data": {
        "user": {
          "id": 1,
          "username": "John Doe",
          "email": "john@example.com"
        },
        "profile": {
          "nik": "1234567890",
          "nama": "John Doe",
          "tempat_lahir": "City",
          "tanggal_lahir": "1990-01-01",
          "jenis_kelamin": "Male",
          "agama": "Islam",
          "status_pekerjaan": "Employed"
        }
      }
    }
    ```
    - **401 Unauthorized**: If the token is invalid or expired.
    - **404 Not Found**: If the user profile does not exist.

#### **PUT /api/update-profile**
- **Description**: Update the profile information of the currently authenticated user.
- **Authentication**: Requires a valid JWT token.
- **Request Body**:
    ```json
    {
      "nik": "string",
      "nama": "string",
      "tempatLahir": "string",
      "tanggalLahir": "date",
      "jenisKelamin": "string",
      "golDarah": "string",
      "alamat": "string",
      "rt": "string",
      "rw": "string",
      "kel": "string",
      "desa": "string",
      "kecamatan": "string",
      "kabupaten": "string",
      "provinsi": "string",
      "agama": "string",
      "statusPekerjaan": "string",
      "statusPerkawinan": "string",
      "pekerjaan": "string",
      "kewarganegaraan": "string",
      "berlakuHingga": "date",
      "kodeBank": "string",
      "noRekening": "string"
    }
    ```
- **Response**:
    - **200 OK**:
    ```json
    {
      "status": "success",
      "message": "Profile updated successfully",
      "data": {
        "nik": "1234567890",
        "nama": "John Doe",
        "tempat_lahir": "City",
        "tanggal_lahir": "1990-01-01",
        "jenis_kelamin": "Male",
        "agama": "Islam",
        "status_pekerjaan": "Employed"
      }
    }
    ```
    - **400 Bad Request**: If validation or bank inquiry fails.
    - **401 Unauthorized**: If the token is invalid or expired.

---

## Response Codes

- **200 OK**: Request was successful.
- **201 Created**: Resource successfully created.
- **400 Bad Request**: Invalid input or request data.
- **401 Unauthorized**: Invalid or missing authentication token.
- **422 Unprocessable Entity**: Validation failed.
- **500 Internal Server Error**: Server encountered an error while processing the request.

---



## Authorization Header Format

For every request that requires authentication, include the following in the header:

```http
Authorization: Bearer <your_jwt_token>
