<tr data-dept-id="{{ $dept->dept_id }}" data-dept='@json($dept)'>
    <td>{{ $dept->dept_id }}</td>
    <td>{{ $dept->dept_code }}</td>
    <td>{{ $dept->dept_name }}</td>
    <td style="display:flex; gap:8px; justify-content:flex-start; align-items:center;">
        <button type="button" data-action="edit" data-id="{{ $dept->dept_id }}">Edit</button>
        <button type="button" data-action="delete" data-id="{{ $dept->dept_id }}" class="btn-secondary">Delete</button>
    </td>
</tr>
