<div>
   <table>
       <thead>
        <th>Name</th>
        <th>Email</th>
        <th>Created At</th>
       </thead>

       <tbody>
       @foreach($users as $user)
           <tr>
               <td>{{ $user->name }}</td>
               <td>{{ $user->email }}</td>
               <td>{{ $user->created_at }}</td>
           </tr>
       @endforeach
       </tbody>
   </table>
</div>
