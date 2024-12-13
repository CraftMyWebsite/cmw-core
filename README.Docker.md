### Building and running CraftMyWebsite with Docker

When you're ready, start your application by running:
`docker compose up --build`.

Your application will be available at http://localhost:80.

### CraftMyWebsite Installation

#### Step 1: Go to Installer http://localhost/installer

#### Step 2: Accept the License Agreement

#### Step 3: Configure your database

**Database credentials**:

- Database Host: `db`
- Database Port: `3306`
- Database Name: `cmw`
- Database User: `user`
- Database Password: `cmw_user_password`

(For admin management only): Root password: `cmw_root_password`

#### Step 4: Follow the installation steps

More help: [Discord](https://craftmywebsite.fr/discord)

### Access phpMyAdmin
Go to [http://localhost:8090](http://localhost:8090) and login with the following credentials:
- user: `root`
- password: `cmw_root_password`