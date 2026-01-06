# sch-enrollment-system

## Backup and Restore

Each CRUD module now supports backup and restore functionality:

### Backup
- Click the "Backup" button in the toolbar to create a JSON backup of all records
- The backup file will be saved in `storage/app/backups/`
- Backup includes all data with related model data loaded

### Restore
- Click the "Restore" button to open the restore modal
- Select a previously created backup JSON file
- Choose restore mode:
  - **Skip**: Only import new records, skip existing ones
  - **Update**: Update existing records and add new ones
  - **Replace**: Delete all current data and replace with backup data

### API Endpoints
Each resource has the following backup/restore endpoints:
- `POST /resource/backup` - Create backup
- `POST /resource/restore` - Restore from backup file
- `GET /resource/backups` - List available backups
- `GET /resource/backups/{filename}` - Download backup file
- `DELETE /resource/backups/{filename}` - Delete backup file

### Supported Resources
- Students
- Programs
- Courses
- Instructors
- Rooms
- Departments
- Enrollments
- Sections
- Terms
- Course Prerequisites