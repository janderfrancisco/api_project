@component('mail::message')
# AVISO USUÁRIO BLOQUEADO:

O usuário ID: {{$data['id']}} - Nome: **{{$data['name']}}**  

foi bloqueado no curso ID: {{$data['course_id']}} - Curso:  **{{$data['course_name']}}** 

módulo ID: {{$data['module_id']}} - Nome:  **{{$data['module_name']}}**
 
 
Obrigado,<br>
{{ config('app.name') }}
@endcomponent
